<?php

namespace App\Http\Controllers;

use App\Helpers\UploadFile;
use App\Models\Cabang;
use App\Models\City;
use App\Models\Customer;
use App\Models\CustomerCategories;
use App\Models\Payments;
use App\Models\Program;
use App\Models\Province;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use RealRashid\SweetAlert\Facades\Alert;
use Yajra\DataTables\DataTables;

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
        // Ambil data customer dengan relasi
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

        $messages = [
            'username.required' => 'Username wajib diisi',
            'username.unique' => 'Username sudah digunakan',
            'email.email' => 'Format email tidak valid',
            'email.unique' => 'Email sudah digunakan',
            'password.required' => 'Password wajib diisi',
            'password.min' => 'Password minimal 6 karakter',
            'name.required' => 'Nama wajib diisi',
            'NIK.required' => 'NIK wajib diisi',
            'NIK.unique' => 'NIK sudah digunakan',
            'sex.required' => 'Jenis kelamin wajib dipilih',
            'phone.required' => 'Nomor telepon wajib diisi',
            'code_province.exists' => 'Provinsi tidak valid',
            'code_city.exists' => 'Kota/Kabupaten tidak valid',
            'code_cabang.exists' => 'Cabang tidak valid',
            'code_mitra.required' => 'Mitra wajib diisi',
            'code_mitra.exists' => 'Mitra tidak valid',
            'code_program.exists' => 'Program tidak valid',
            'birth_date.date' => 'Tanggal lahir tidak valid',
            'picture_ktp.required' => 'Foto KTP wajib diunggah',
            'picture_ktp.image' => 'File foto KTP harus berupa gambar',
            'picture_ktp.mimes' => 'Format foto KTP harus jpeg, png, atau jpg',
            'picture_ktp.max' => 'Ukuran foto KTP maksimal 2MB',
            'status_prospek.required' => 'Status prospek wajib dipilih',
            'status_prospek.in' => 'Status prospek tidak valid',
        ];


        $validator = Validator::make($request->all(), [
            'username' => 'required|unique:customers,username',
            'email' => 'nullable|email|unique:customers,email',
            'password' => 'required|min:6',
            'name' => 'required|string|max:255',
            'NIK' => 'required|unique:customers,NIK|digits:16',
            'sex' => 'required|in:L,P',
            'phone' => 'required|string|max:20',
            'code_province' => 'nullable|exists:provinces,code',
            'code_city' => 'nullable|exists:cities,code',
            'code_cabang' => 'nullable|exists:cabangs,code',
            'code_mitra' => 'required|exists:mitras,code',
            'code_category' => 'nullable|exists:customer_categories,code',
            'code_program' => 'nullable|exists:programs,code',
            'birth_date' => 'nullable|date',
            'picture_ktp' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'status_prospek' => 'required|in:cold,warm,hot',
        ], $messages);


        if ($validator->fails()) {
            Alert::error('Error', $validator->errors()->first());
            return back()->withErrors($validator)->withInput();
        }

        try {
            DB::beginTransaction();
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
            $picture_ktp = null;
            if ($request->hasFile('picture_ktp')) {
                $picture_ktp = UploadFile::file($request->file('picture_ktp'), 'customer/ktp');
            }

            $loggedInMitra = Auth::guard('mitra')->user();
            $codeMitra = $loggedInMitra->code ?? null;

            Customer::create([
                'name' => $request->name,
                'code' => $newCode,
                'username' => $request->username,
                'password' => Hash::make($request->password),
                'phone' => $request->phone,
                'job' => $request->job,
                'email' => $request->email,
                'code_category' => $request->code_category,
                'code_cabang' => $request->code_cabang,
                'code_mitra' => $codeMitra,
                'code_city' => $request->code_city,
                'code_province' => $request->code_province,
                'note' => $request->note,
                'status' => 'prospek',
                'status_prospek' => $request->status_prospek,
                'status_jamaah' => 'nonactive',
                'status_alumni' => 'nonactive',
                'address' => $request->address,
                'code_program' => $request->code_program,
                'NIK' => $request->NIK,
                'birth_place' => $request->birth_place,
                'birth_date' => $request->birth_date,
                'picture_ktp' => $picture_ktp,
            ]);
            DB::commit();
            Alert::success('Berhasil', 'Data Customer berhasil ditambahkan')
                ->persistent(true)
                ->autoClose(5000);

            return redirect()->route('customer.create');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Customer Registration Error: ' . $e->getMessage());

            if (isset($picture_ktp)) {
                UploadFile::delete('customer/ktp', $picture_ktp);
            }
            Alert::error('Error', 'Terjadi kesalahan pada sistem. Silakan coba lagi.')
                ->persistent(true)
                ->autoClose(5000);

            return back()->withInput();
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
}