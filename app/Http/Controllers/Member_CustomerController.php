<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\Cabang;
use App\Models\Jamaah;
use App\Models\Program;
use App\Models\Regency;
use App\Models\Customer;
use App\Models\Payments;
use App\Models\Province;
use App\Helpers\UploadFile;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Models\CustomerCategories;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\Validator;

class Member_CustomerController extends Controller
{
    public function list(Request $request)
    {
        if ($request->ajax()) {
            $loggedInMitra = Auth::guard('mitra')->user();
            $loggedInCode = $loggedInMitra->code ?? null;
            $data = Customer::query()
                ->select([
                    'id',
                    'name',
                    'username',
                    'email',
                    'phone',
                    'status',
                    'status_prospek',
                    'picture_ktp',
                    'code',
                    'code_mitra',
                ])
                ->where('code_mitra', $loggedInCode)
                ->orderBy('name', 'asc');

            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('email', function ($row) {
                    return $row->email ?? '-';
                })
                ->editColumn('phone', function ($row) {
                    return $row->phone ?? '-';
                })
                ->editColumn('status', function ($row) {
                    $statusClass = [
                        'prospek' => 'bg-label-warning',
                        'jamaah' => 'bg-label-success',
                        'alumni' => 'bg-label-secondary',
                    ];
                    $statusLabel = ucfirst($row->status);
                    return '<span class="badge rounded-pill ' . $statusClass[$row->status] . '">' . $statusLabel . '</span>';
                })
                ->editColumn('status_prospek', function ($row) {
                    $prospekClass = [
                        'cold' => 'bg-label-danger',
                        'warm' => 'bg-label-warning',
                        'hot' => 'bg-label-success',
                    ];
                    return '<span class="badge rounded-pill ' . $prospekClass[$row->status_prospek] . '">' . ucfirst($row->status_prospek) . '</span>';
                })
                ->addColumn('full_name', function ($row) {
                    $avatar = $row->picture_ktp ?
                        '<img src="' . $row->picture_ktp . '" alt="Avatar" class="rounded-circle" width="32">' :
                        '<span class="avatar-initial rounded-circle bg-label-primary">' . strtoupper(substr($row->name ?? 'U', 0, 2)) . '</span>';

                    return '<div class="d-flex justify-content-start align-items-center">
                            <div class="avatar me-2">' . $avatar . '</div>
                            <div class="d-flex flex-column">
                                <span class="text-truncate">' . ($row->name ?? 'Unnamed') . '</span>
                                <small class="text-muted">' . ($row->code ?? '-') . '</small>
                            </div>
                        </div>';
                })
                ->addColumn('action', function ($row) {
                    return '
                        <a href="' . route('customer.edit', $row->id) . '" class="btn btn-sm btn-primary">Edit</a>
                        <button type="button" class="btn btn-sm btn-info" 
                            onclick="showPaymentModal(\'' . $row->code . '\')">
                            <i class="ri-money-dollar-circle-line me-1"></i>
                            Status Pembayaran
                         </button>
                    ';
                })
                ->rawColumns(['full_name', 'status', 'status_prospek', 'action'])
                ->make(true);
        }
        $title = "Daftar Customer Anda";
        return view('pages.customer.list', compact('title'));
    }

    public function show($id)
    {
        $title = "Detail Customer";

        $customer = Customer::with(['mitra', 'cabang', 'program', 'city', 'province'])
            ->findOrFail($id);

        return view('pages.customer.detail-customer', compact('customer', 'title'));
    }


    public function create()
    {
        $title = "Tambah / Daftarkan Customer Anda";


        $provinces = Province::all();
        $cities = City::all();
        $cabangs = Cabang::all();
        $categories = CustomerCategories::all();
        $programs = Program::all();


        $loggedInMitra = Auth::guard('mitra')->user();
        $mitraInfo = $loggedInMitra
            ? $loggedInMitra->name . ' (' . $loggedInMitra->code . ')'
            : null;

        return view('pages.customer.pendaftaran', compact('title', 'provinces', 'cities', 'cabangs', 'categories', 'programs', 'mitraInfo'));
    }

    public function edit($id)
    {
        $title = "Edit Customer";
        $customer = Customer::findOrFail($id);
        $provinces = Province::all();
        $cities = City::all();
        $cabangs = Cabang::all();
        $categories = CustomerCategories::all();
        $programs = Program::all();

        return view('pages.customer.edit', compact('title', 'customer', 'provinces', 'cities', 'cabangs', 'categories', 'programs'));
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            Log::info('Memulai proses pembuatan customer baru', [
                'request_data' => $request->all()
            ]);
            $lastCode = DB::table('customers')
                ->whereNotNull('code')
                ->orderBy('code', 'desc')
                ->lockForUpdate()
                ->value('code');
            $newCodeNumber = ($lastCode ? intval($lastCode) + 1 : 1);
            $newCode = str_pad($newCodeNumber, 10, '0', STR_PAD_LEFT);

            Log::info('Generated new code', [
                'last_code' => $lastCode,
                'new_code' => $newCode
            ]);

            $picture_ktp = null;
            if ($request->hasFile('picture_ktp')) {
                try {
                    $picture_ktp = UploadFile::file($request->file('picture_ktp'), 'customer/ktp');
                    Log::info('Berhasil upload KTP', [
                        'filename' => $picture_ktp
                    ]);
                } catch (\Exception $e) {
                    Log::error('Gagal upload KTP', [
                        'error' => $e->getMessage(),
                        'file' => $request->file('picture_ktp')
                    ]);
                    throw $e;
                }
            }

            $loggedInMitra = Auth::guard('mitra')->user();
            Log::info('Data mitra', [
                'mitra_code' => $loggedInMitra->code ?? 'null',
                'mitra_data' => $loggedInMitra ?? 'not logged in'
            ]);

            $customerData = [
                'name' => $request->name,
                'code' => $newCode,
                'username' => '',
                'password' => '',
                'email' => '',
                'code_category' => '',
                'code_cabang' => '',
                'code_mitra' => $loggedInMitra->code ?? null,
                'code_city' => $request->code_city,
                'code_province' => $request->code_province,
                'phone' => $request->phone,
                'note' => $request->note,
                'status' => 'prospek',
                'status_prospek' => 'cold',
                'status_jamaah' => 'nonactive',
                'status_alumni' => 'nonactive',
                'address' => $request->address,
                'code_program' => $request->code_program,
                'NIK' => $request->NIK,
                'birth_place' => $request->birth_place,
                'birth_date' => $request->birth_date,
                'picture_ktp' => $picture_ktp,
            ];

            Log::info('Mencoba membuat customer baru dengan data:', [
                'customer_data' => $customerData
            ]);

            $customer = Customer::create($customerData);

            Log::info('Customer berhasil dibuat', [
                'customer_id' => $customer->id,
                'customer_code' => $customer->code
            ]);

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Data Customer berhasil ditambahkan'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error saat membuat customer:', [
                'error_message' => $e->getMessage(),
                'error_line' => $e->getLine(),
                'error_file' => $e->getFile(),
                'error_trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);

            if (isset($picture_ktp)) {
                try {
                    UploadFile::delete('customer/ktp', $picture_ktp);
                    Log::info('Berhasil menghapus file KTP setelah error', [
                        'filename' => $picture_ktp
                    ]);
                } catch (\Exception $deleteError) {
                    Log::error('Gagal menghapus file KTP setelah error', [
                        'filename' => $picture_ktp,
                        'error' => $deleteError->getMessage()
                    ]);
                }
            }

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan pada sistem. Error: ' . $e->getMessage()
            ], 500);
        }
    }


    public function update(Request $request, $id)
    {
        $customer = Customer::findOrFail($id);

        $messages = [
            'username.required' => 'Username wajib diisi',
            'username.unique' => 'Username sudah digunakan',
            'email.email' => 'Format email tidak valid',
            'email.unique' => 'Email sudah digunakan',
            'name.required' => 'Nama wajib diisi',
            'NIK.required' => 'NIK wajib diisi',
            'sex.required' => 'Jenis kelamin wajib dipilih',
            'phone.required' => 'Nomor telepon wajib diisi',
            'code_cabang.required' => 'Cabang wajib dipilih',
            'code_mitra.required' => 'Mitra wajib diisi',
            'picture_ktp.image' => 'File foto KTP harus berupa gambar',
            'picture_ktp.mimes' => 'Format foto KTP harus jpeg, png, atau jpg',
            'picture_ktp.max' => 'Ukuran foto KTP maksimal 2MB',
        ];

        $validator = Validator::make($request->all(), [
            'username' => 'required|unique:customers,username,' . $customer->id,
            'email' => 'nullable|email|unique:customers,email,' . $customer->id,
            'name' => 'required',
            'NIK' => 'required|unique:customers,NIK,' . $customer->id,
            'sex' => 'required|in:L,P',
            'phone' => 'required',
            'code_province' => 'nullable|exists:provinces,code',
            'code_city' => 'nullable|exists:cities,code',
            'code_cabang' => 'nullable|exists:cabangs,code',
            'code_mitra' => 'required|exists:mitras,code',
            'code_category' => 'nullable|exists:customer_categories,code',
            'code_program' => 'nullable|exists:programs,code',
            'birth_date' => 'nullable|date',
            'picture_ktp' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ], $messages);

        if ($validator->fails()) {
            Alert::error('Error', $validator->errors()->first());
            return back()->withErrors($validator)->withInput();
        }

        try {
            DB::beginTransaction();


            if ($request->hasFile('picture_ktp')) {

                if ($customer->picture_ktp) {
                    UploadFile::delete('customer/ktp', $customer->picture_ktp);
                }
                $picture_ktp = UploadFile::file($request->file('picture_ktp'), 'customer/ktp');
                $customer->picture_ktp = $picture_ktp;
            }


            $customer->update([
                'name' => $request->name,
                'username' => $request->username,
                'password' => $request->password ? Hash::make($request->password) : $customer->password,
                'phone' => $request->phone,
                'job' => $request->job,
                'email' => $request->email,
                'code_category' => $request->code_category,
                'code_cabang' => $request->code_cabang,
                'code_mitra' => $request->code_mitra,
                'code_city' => $request->code_city,
                'code_province' => $request->code_province,
                'note' => $request->note,
                'address' => $request->address,
                'code_program' => $request->code_program,
                'NIK' => $request->NIK,
                'birth_place' => $request->birth_place,
                'birth_date' => $request->birth_date,

            ]);

            DB::commit();

            Alert::success('Berhasil', 'Data Customer berhasil diupdate')
                ->persistent(true)
                ->autoClose(5000);
            return redirect()->route('customer.list');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Customer Update Error: ' . $e->getMessage());

            Alert::error('Error', 'Terjadi kesalahan pada sistem. Silakan coba lagi.')
                ->persistent(true)
                ->autoClose(5000);
            return back()->withInput();
        }
    }

    public function destroy($id)
    {
        $customer = Customer::findOrFail($id);

        try {

            if ($customer->picture_ktp) {
                UploadFile::delete('customer/ktp', $customer->picture_ktp);
            }

            $customer->delete();

            Alert::success('Berhasil', 'Customer berhasil dihapus')
                ->persistent(true)
                ->autoClose(5000);
            return redirect()->route('customer.list');
        } catch (\Exception $e) {
            Log::error('Customer Delete Error: ' . $e->getMessage());

            Alert::error('Error', 'Terjadi kesalahan pada sistem. Silakan coba lagi.')
                ->persistent(true)
                ->autoClose(5000);
            return back();
        }
    }

    public function getPayments($code)
    {
        try {
            $customer = Customer::where('code', $code)->firstOrFail();
            $payments = Payments::where('code_customer', $code)
                ->orderBy('tanggal_transaksi', 'desc')
                ->get();

            $programInfo = null;
            if ($payments->isNotEmpty()) {
                $programCode = $payments->first()->code_program;
                $program = Program::where('code', $programCode)->first();
                if ($program) {
                    $programInfo = $program->name;
                }
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'customer_name' => $customer->name,
                    'program_name' => $programInfo,
                    'payments' => $payments
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data pembayaran'
            ], 500);
        }
    }


    public function getAllDataCustomer(Request $request)
    {
        $codeMitra = $request->header('code_mitra');

        $customers = Customer::with([
            'category:code,name',
            'cabang:code,name',
            'city:id,name',
            'province:id,name',
            'program:code,name',
            'mitra:code,name',
            'jamaah:code,status_payment,status_berangkat'
        ])
            ->where('code_mitra', $codeMitra)
            ->select([
                'id',
                'code',
                'username',
                'name',
                'phone',
                'email',
                'code_category',
                'code_cabang',
                'code_mitra',
                'code_city',
                'code_province',
                'status',
                'status_prospek',
                'status_jamaah',
                'status_alumni',
                'address',
                'code_program',
                'NIK',
                'birth_place',
                'birth_date',
                'picture_ktp'
            ])
            ->get();

        if ($customers->isEmpty()) {
            return response()->json(['status' => false, 'message' => 'Data customer tidak ditemukan', 'data' => null], 404);
        }

        $transformedData = $customers->map(function ($item) {
            $jamaahCode = null;
            if ($item->status_jamaah === 'active' && $item->jamaah) {
                $jamaahCode = $item->jamaah->code;
            }

            return [
                'id' => $item->id,
                'code' => $item->code,
                'username' => $item->username,
                'name' => $item->name,
                'phone' => $item->phone,
                'email' => $item->email,
                'status' => $item->status,
                'status_prospek' => $item->status_prospek,
                'status_jamaah' => $item->status_jamaah,
                'status_alumni' => $item->status_alumni,
                'address' => $item->address,
                'NIK' => $item->NIK,
                'birth_place' => $item->birth_place,
                'birth_date' => $item->birth_date,
                'picture_ktp' => $item->picture_ktp,
                'code_jamaah' => $jamaahCode,
                'category' => $item->category ? ['code' => $item->category->code, 'name' => $item->category->name] : null,
                'cabang' => $item->cabang ? ['code' => $item->cabang->code, 'name' => $item->cabang->name] : null,
                'city' => $item->city ? ['code' => $item->city->code, 'name' => $item->city->name] : null,
                'province' => $item->province ? ['code' => $item->province->code, 'name' => $item->province->name] : null,
                'program' => $item->program ? ['code' => $item->program->code, 'name' => $item->program->name] : null,
                'mitra' => $item->mitra ? ['code' => $item->mitra->code, 'name' => $item->mitra->name] : null,
                'jamaah' => $item->jamaah ? [
                    'code' => $item->jamaah->code,
                    'status_payment' => $item->jamaah->status_payment,
                    'status_berangkat' => $item->jamaah->status_berangkat
                ] : null,
            ];
        });

        return response()->json(['status' => true, 'message' => 'Data customer berhasil diambil', 'data' => $transformedData], 200);
    }
    public function getDetailCustomer($id)
    {
        try {
            $customer = Customer::with([
                'category',
                'cabang',
                'city',
                'province',
                'program',
                'mitra',
                'payments',
                'paymentConfirms',
                'jamaah'
            ])
                ->findOrFail($id);

            $jamaahCode = null;
            if ($customer->status_jamaah === 'active' && $customer->jamaah) {
                $jamaahCode = $customer->jamaah->code;
                Log::info("Found jamaah code: $jamaahCode");
            }
            $programInfo = null;
            if ($customer->program) {
                $programInfo = [
                    'code' => $customer->program->code,
                    'name' => $customer->program->name,
                    'sisa_kursi' => $customer->program->sisa_kursi,
                    'status' => $customer->program->status,
                    'is_available' => $customer->program->status === 'active' && $customer->program->sisa_kursi > 0
                ];
            }

            $transformedData = [
                'id' => $customer->id,
                'code' => $customer->code,
                'username' => $customer->username,
                'name' => $customer->name,
                'phone' => $customer->phone,
                'email' => $customer->email,
                'status' => $customer->status,
                'status_prospek' => $customer->status_prospek,
                'status_jamaah' => $customer->status_jamaah,
                'status_alumni' => $customer->status_alumni,
                'address' => $customer->address,
                'NIK' => $customer->NIK,
                'birth_place' => $customer->birth_place,
                'birth_date' => $customer->birth_date,
                'picture_ktp' => $customer->picture_ktp,
                'code_program' => $customer->code_program,
                'code_jamaah' => $jamaahCode,
                'note' => $customer->note,
                'category' => $customer->category ? ['code' => $customer->category->code, 'name' => $customer->category->name] : null,
                'cabang' => $customer->cabang ? ['code' => $customer->cabang->code, 'name' => $customer->cabang->name] : null,
                'city' => $customer->city ? ['id' => $customer->city->id, 'name' => $customer->city->name] : null,
                'province' => $customer->province ? ['id' => $customer->province->id, 'name' => $customer->province->name] : null,
                'program' => $programInfo,
                'mitra' => $customer->mitra ? ['code' => $customer->mitra->code, 'name' => $customer->mitra->name] : null,
                'jamaah' => $customer->jamaah ? [
                    'code' => $customer->jamaah->code,
                    'status_payment' => $customer->jamaah->status_payment,
                    'status_berangkat' => $customer->jamaah->status_berangkat,
                    'tahun_jamaah' => $customer->jamaah->tahun_jamaah,
                ] : null,
                'payments' => $customer->payments->map(function ($payment) {
                    return [
                        'id' => $payment->id,
                        'code' => $payment->code,
                        'value' => $payment->value,
                        'status_payment' => $payment->status_payment,
                        'tanggal_transaksi' => $payment->tanggal_transaksi,
                        'code_transaksi' => $payment->code_transaksi
                    ];
                }),
                'payment_confirms' => $customer->paymentConfirms->map(function ($confirm) {
                    return [
                        'id' => $confirm->id,
                        'code' => $confirm->code,
                        'value' => $confirm->value,
                        'status_payment' => $confirm->status_payment,
                        'tanggal_transaksi' => $confirm->tanggal_transaksi,
                        'code_transaksi' => $confirm->code_transaksi,
                        'picture_scan' => $confirm->picture_scan
                    ];
                })
            ];

            return response()->json([
                'status' => true,
                'message' => 'Detail customer berhasil diambil',
                'data' => $transformedData
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Data customer tidak ditemukan: ' . $e->getMessage(),
                'data' => null
            ], 404);
        }
    }
    public function getRelationalData()
    {
        try {
            $cabang = Cabang::all();
            $city = City::all();
            $province = Province::all();
            $category = CustomerCategories::all();


            $program = Program::where('status', 'active')
                ->where('sisa_kursi', '>', 0)
                ->get();

            return response()->json([
                'status' => 'success',
                'message' => 'Data relasi berhasil diambil',
                'cabang' => $cabang,
                'city' => $city,
                'province' => $province,
                'category' => $category,
                'program' => $program
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengambil data relasi: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getRelationalDataForEdit($customerId)
    {
        try {
            $customer = Customer::findOrFail($customerId);
            $currentProgramCode = $customer->code_program;

            $cabang = Cabang::all();
            $city = City::all();
            $province = Province::all();
            $category = CustomerCategories::all();



            $program = Program::where(function ($query) use ($currentProgramCode) {

                $query->where('status', 'active')
                    ->where('sisa_kursi', '>', 0);
            })
                ->orWhere(function ($query) use ($currentProgramCode) {

                    if ($currentProgramCode) {
                        $query->where('code', $currentProgramCode);
                    }
                })
                ->distinct()
                ->get();


            $program = $program->map(function ($prog) use ($currentProgramCode) {
                $prog->is_current = $prog->code === $currentProgramCode;
                $prog->is_available = $prog->status === 'active' && $prog->sisa_kursi > 0;
                return $prog;
            });

            return response()->json([
                'status' => 'success',
                'message' => 'Data relasi berhasil diambil',
                'cabang' => $cabang,
                'city' => $city,
                'province' => $province,
                'category' => $category,
                'program' => $program
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengambil data relasi: ' . $e->getMessage()
            ], 500);
        }
    }

    public function storeCustomerApi(Request $request)
    {
        $messages = [
            'code_mitra.required' => 'Code mitra tidak ditemukan',
            'name.required' => 'Nama wajib diisi',
            'phone.required' => 'Nomor telepon wajib diisi',
            'phone.regex' => 'Format nomor telepon tidak valid (gunakan format: 08xxxxxxxxx)',
            'phone.unique' => 'Nomor telepon sudah terdaftar',
            'NIK.regex' => 'NIK harus 16 digit angka',
            'NIK.unique' => 'NIK sudah terdaftar',
            'code_program.required' => 'Program wajib dipilih',
            'code_program.exists' => 'Program tidak ditemukan',
            'picture_ktp.image' => 'File foto KTP harus berupa gambar',
            'picture_ktp.mimes' => 'Format foto KTP harus jpeg, png, atau jpg',
            'picture_ktp.max' => 'Ukuran foto KTP maksimal 2MB',
            'province_id.exists' => 'Provinsi tidak valid',
            'regency_id.exists' => 'Kota/Kabupaten tidak valid',
        ];

        $validator = Validator::make($request->all(), [
            'code_mitra' => 'required',
            'name' => 'required',
            'phone' => [
                'required',
                'regex:/^08[0-9]{8,11}$/',
                'unique:customers,phone'
            ],
            'NIK' => 'nullable|regex:/^[0-9]{16}$/|unique:customers,NIK',
            'code_program' => 'required|exists:programs,code',
            'province_id' => 'nullable|exists:provinces,id',
            'regency_id' => 'nullable|exists:regencies,id',
            'picture_ktp' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ], $messages);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first()
            ], 400);
        }

        DB::beginTransaction();
        try {
            $lastCode = DB::table('customers')
                ->whereNotNull('code')
                ->orderBy('code', 'desc')
                ->lockForUpdate()
                ->value('code');

            $newCodeNumber = ($lastCode ? intval($lastCode) + 1 : 1);
            $newCode = str_pad($newCodeNumber, 10, '0', STR_PAD_LEFT);

            while (DB::table('customers')->where('code', $newCode)->exists()) {
                $newCodeNumber++;
                $newCode = str_pad($newCodeNumber, 10, '0', STR_PAD_LEFT);
            }

            $picture_ktp = $request->hasFile('picture_ktp') ?
                UploadFile::file($request->file('picture_ktp'), 'customer/ktp') : null;
            $uniqueUsername = 'user_' . time() . '_' . Str::random(8);

            $province = null;
            $regency = null;

            if ($request->province_id) {
                $province = Province::find($request->province_id);
            }

            if ($request->regency_id) {
                $regency = Regency::find($request->regency_id);
            }

            $customer = Customer::create([
                'code' => $newCode,
                'username' => $uniqueUsername,
                'password' => Hash::make('password123'),
                'name' => $request->name,
                'phone' => $request->phone,
                'job' => $request->job,
                'sex' => $request->sex,
                'email' => null,
                'code_category' => null,
                'code_cabang' => null,
                'code_mitra' => $request->code_mitra,
                'code_city' => $regency ? $regency->id : null,
                'code_province' => $province ? $province->id : null,
                'note' => $request->note,
                'status' => 'prospek',
                'status_prospek' => 'cold',
                'status_jamaah' => 'nonactive',
                'status_alumni' => 'nonactive',
                'address' => $request->address,
                'code_program' => $request->code_program,
                'NIK' => $request->NIK,
                'birth_place' => $request->birth_place,
                'birth_date' => $request->birth_date,
                'picture_ktp' => $picture_ktp,
            ]);
            if (!$this->updateProgramQuota($request->code_program, 'decrease')) {
                throw new \Exception('Gagal mengurangi sisa kursi program');
            }

            DB::commit();

            $responseData = $customer->toArray();
            if ($province) {
                $responseData['province'] = [
                    'id' => $province->id,
                    'name' => $province->name
                ];
            }
            if ($regency) {
                $responseData['regency'] = [
                    'id' => $regency->id,
                    'name' => $regency->name
                ];
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Data Customer berhasil ditambahkan',
                'data' => $responseData
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();

            if (isset($picture_ktp)) {
                UploadFile::delete('customer/ktp', $picture_ktp);
            }

            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan pada sistem: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateCustomerApi(Request $request, $id)
    {
        try {
            $customer = Customer::findOrFail($id);

            $messages = [
                'name.required' => 'Nama wajib diisi',
                'phone.required' => 'Nomor telepon wajib diisi',
                'phone.regex' => 'Format nomor telepon tidak valid (gunakan format: 08xxxxxxxxx)',
                'phone.unique' => 'Nomor telepon sudah terdaftar',
                'NIK.regex' => 'NIK harus 16 digit angka',
                'NIK.unique' => 'NIK sudah terdaftar',
                'picture_ktp.image' => 'File foto KTP harus berupa gambar',
                'picture_ktp.mimes' => 'Format foto KTP harus jpeg, png, atau jpg',
                'picture_ktp.max' => 'Ukuran foto KTP maksimal 2MB',
                'status_prospek.in' => 'Status prospek harus cold, warm, atau hot',
                'status_jamaah.in' => 'Status jamaah harus active atau nonactive',
            ];

            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'phone' => [
                    'required',
                    'regex:/^08[0-9]{8,11}$/',
                    'unique:customers,phone,' . $customer->id
                ],
                'NIK' => 'nullable|regex:/^[0-9]{16}$/|unique:customers,NIK,' . $customer->id,
                'sex' => 'nullable|in:L,P',
                'province_id' => 'nullable|exists:provinces,id',
                'regency_id' => 'nullable|exists:regencies,id',
                'code_program' => 'nullable|exists:programs,code',
                'birth_date' => 'nullable|date',
                'picture_ktp' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
                'status_prospek' => 'nullable|in:cold,warm,hot',
                'status_jamaah' => 'nullable|in:active,nonactive',
            ], $messages);

            $oldProgramCode = $customer->code_program;
            $newProgramCode = $request->code_program;
            if ($newProgramCode && $newProgramCode !== $oldProgramCode) {
                $newProgram = Program::where('code', $newProgramCode)->first();
                if (!$newProgram) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Program baru tidak ditemukan'
                    ], 400);
                }

                if ($newProgram->sisa_kursi <= 0) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Maaf, kursi untuk program baru ini sudah penuh'
                    ], 400);
                }
            }

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => $validator->errors()->first()
                ], 400);
            }

            DB::beginTransaction();
            $picture_ktp = $customer->picture_ktp;
            if ($request->hasFile('picture_ktp')) {
                if ($customer->picture_ktp) {
                    UploadFile::delete('customer/ktp', $customer->picture_ktp);
                }
                $picture_ktp = UploadFile::file($request->file('picture_ktp'), 'customer/ktp');
            }
            $oldStatusJamaah = $customer->status_jamaah;
            $newStatusJamaah = $request->status_jamaah ?? $customer->status_jamaah;

            $customer->update([
                'name' => $request->name,
                'phone' => $request->phone,
                'job' => $request->job,
                'code_city' => $request->regency_id,
                'code_province' => $request->province_id,
                'note' => $request->note,
                'address' => $request->address,
                'code_program' => $request->code_program,
                'NIK' => $request->NIK,
                'birth_place' => $request->birth_place,
                'birth_date' => $request->birth_date,
                'sex' => $request->sex,
                'picture_ktp' => $picture_ktp,
                'status_prospek' => $request->status_prospek,
                'status_jamaah' => $newStatusJamaah,
            ]);


            if ($request->has('status_jamaah') && $oldStatusJamaah !== $newStatusJamaah) {
                $this->handleJamaahStatusChange($customer, $oldStatusJamaah, $newStatusJamaah);
            }


            if ($oldProgramCode !== $newProgramCode) {
                if ($oldProgramCode) {
                    $this->updateProgramQuota($oldProgramCode, 'increase');
                }
                if ($newProgramCode) {
                    if (!$this->updateProgramQuota($newProgramCode, 'decrease')) {
                        throw new \Exception('Gagal mengurangi sisa kursi program baru');
                    }
                }
            }

            DB::commit();
            $updatedCustomer = Customer::with([
                'category:code,name',
                'cabang:code,name',
                'city:id,name',
                'province:id,name',
                'program:code,name',
                'mitra:code,name',
                'jamaah'
            ])->findOrFail($id);
            $jamaahCode = null;
            if ($updatedCustomer->status_jamaah === 'active' && $updatedCustomer->jamaah) {
                $jamaahCode = $updatedCustomer->jamaah->code;
            }

            $transformedData = [
                'id' => $updatedCustomer->id,
                'code' => $updatedCustomer->code,
                'username' => $updatedCustomer->username,
                'name' => $updatedCustomer->name,
                'phone' => $updatedCustomer->phone,
                'email' => $updatedCustomer->email,
                'status' => $updatedCustomer->status,
                'status_prospek' => $updatedCustomer->status_prospek,
                'status_jamaah' => $updatedCustomer->status_jamaah,
                'status_alumni' => $updatedCustomer->status_alumni,
                'address' => $updatedCustomer->address,
                'NIK' => $updatedCustomer->NIK,
                'birth_place' => $updatedCustomer->birth_place,
                'birth_date' => $updatedCustomer->birth_date,
                'picture_ktp' => $updatedCustomer->picture_ktp,
                'job' => $updatedCustomer->job,
                'sex' => $updatedCustomer->sex,
                'note' => $updatedCustomer->note,
                'code_jamaah' => $jamaahCode,
                'category' => $updatedCustomer->category ? ['code' => $updatedCustomer->category->code, 'name' => $updatedCustomer->category->name] : null,
                'cabang' => $updatedCustomer->cabang ? ['code' => $updatedCustomer->cabang->code, 'name' => $updatedCustomer->cabang->name] : null,
                'city' => $updatedCustomer->city ? ['id' => $updatedCustomer->city->id, 'name' => $updatedCustomer->city->name] : null,
                'province' => $updatedCustomer->province ? ['id' => $updatedCustomer->province->id, 'name' => $updatedCustomer->province->name] : null,
                'program' => $updatedCustomer->program ? ['code' => $updatedCustomer->program->code, 'name' => $updatedCustomer->program->name] : null,
                'mitra' => $updatedCustomer->mitra ? ['code' => $updatedCustomer->mitra->code, 'name' => $updatedCustomer->mitra->name] : null,
                'jamaah' => $updatedCustomer->jamaah ? [
                    'code' => $updatedCustomer->jamaah->code,
                    'status_payment' => $updatedCustomer->jamaah->status_payment,
                    'status_berangkat' => $updatedCustomer->jamaah->status_berangkat,
                    'tahun_jamaah' => $updatedCustomer->jamaah->tahun_jamaah,
                    'status' => $updatedCustomer->jamaah->status,
                ] : null,
            ];

            return response()->json([
                'status' => 'success',
                'message' => 'Data Customer berhasil diupdate',
                'data' => $transformedData
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan pada sistem: ' . $e->getMessage()
            ], 500);
        }
    }
    private function updateProgramQuota($programCode, $action = 'decrease')
    {
        try {
            $program = Program::where('code', $programCode)->first();

            if (!$program) {
                Log::warning('Program not found for quota update', ['program_code' => $programCode]);
                return false;
            }

            if ($action === 'decrease') {
                if ($program->sisa_kursi > 0) {
                    $program->decrement('sisa_kursi');
                    Log::info('Program sisa_kursi decreased', [
                        'program_code' => $programCode,
                        'remaining_seats' => $program->fresh()->sisa_kursi
                    ]);
                } else {
                    Log::warning('Cannot decrease sisa_kursi, already at 0', ['program_code' => $programCode]);
                    return false;
                }
            } else if ($action === 'increase') {
                $program->increment('sisa_kursi');
                Log::info('Program sisa_kursi increased', [
                    'program_code' => $programCode,
                    'remaining_seats' => $program->fresh()->sisa_kursi
                ]);
            }

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to update program quota: ' . $e->getMessage(), [
                'program_code' => $programCode,
                'action' => $action
            ]);
            return false;
        }
    }
    private function handleJamaahStatusChange($customer, $oldStatus, $newStatus)
    {
        try {
            Log::info('Handling jamaah status change', [
                'customer_code' => $customer->code,
                'old_status' => $oldStatus,
                'new_status' => $newStatus
            ]);


            if ($newStatus === 'active' && $oldStatus !== 'active') {


                $existingJamaah = Jamaah::where('code_customer', $customer->code)
                    ->where('status', 'active')
                    ->first();

                Log::info('Checking existing jamaah', [
                    'customer_code' => $customer->code,
                    'existing_jamaah' => $existingJamaah ? $existingJamaah->code : 'none'
                ]);

                if (!$existingJamaah) {

                    $lastJamaahCode = DB::table('jamaahs')
                        ->whereNotNull('code')
                        ->orderBy('code', 'desc')
                        ->lockForUpdate()
                        ->value('code');


                    $lastNumber = $lastJamaahCode ? intval(substr($lastJamaahCode, 1)) : 0;
                    $newNumber = $lastNumber + 1;
                    $newJamaahCode = 'J' . str_pad($newNumber, 10, '0', STR_PAD_LEFT);


                    while (DB::table('jamaahs')->where('code', $newJamaahCode)->exists()) {
                        $newNumber++;
                        $newJamaahCode = 'J' . str_pad($newNumber, 10, '0', STR_PAD_LEFT);
                    }

                    Log::info('Generated jamaah code', [
                        'customer_code' => $customer->code,
                        'jamaah_code' => $newJamaahCode
                    ]);


                    $jamaahData = [
                        'code' => $newJamaahCode,
                        'name' => $customer->name,
                        'phone' => $customer->phone,
                        'email' => $customer->email,
                        'job' => $customer->job,
                        'city_program' => null,
                        'desc' => $customer->note,
                        'date_program' => null,
                        'value' => null,
                        'total_payment' => 0,
                        'code_city' => $customer->code_city,
                        'code_province' => $customer->code_province,
                        'code_customer' => $customer->code,
                        'code_program' => $customer->code_program,
                        'code_cabang' => $customer->code_cabang,
                        'code_mitra' => $customer->code_mitra,
                        'status_payment' => 'dp',
                        'status_berangkat' => 'belum',
                        'picture_profile' => null,
                        'picture_ktp' => $customer->picture_ktp,
                        'status' => 'active',
                        'tahun_jamaah' => date('Y'),
                    ];

                    Log::info('Creating jamaah with data', [
                        'jamaah_data' => $jamaahData
                    ]);


                    $jamaah = Jamaah::create($jamaahData);

                    if ($jamaah) {
                        Log::info('Jamaah created successfully', [
                            'customer_code' => $customer->code,
                            'jamaah_code' => $jamaah->code,
                            'jamaah_id' => $jamaah->id
                        ]);


                        $customer->update(['status' => 'jamaah']);
                    } else {
                        Log::error('Failed to create jamaah', [
                            'customer_code' => $customer->code
                        ]);
                        throw new \Exception('Gagal membuat data jamaah');
                    }
                } else {
                    Log::info('Jamaah already exists, activating it', [
                        'customer_code' => $customer->code,
                        'jamaah_code' => $existingJamaah->code
                    ]);


                    $existingJamaah->update(['status' => 'active']);
                }
            }


            if ($newStatus === 'nonactive' && $oldStatus === 'active') {
                $affectedRows = Jamaah::where('code_customer', $customer->code)
                    ->where('status', 'active')
                    ->update(['status' => 'nonactive']);

                Log::info('Jamaah deactivated from customer update', [
                    'customer_code' => $customer->code,
                    'affected_rows' => $affectedRows,
                    'status_jamaah' => $newStatus
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Error handling jamaah status change: ' . $e->getMessage(), [
                'customer_code' => $customer->code,
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }
}
