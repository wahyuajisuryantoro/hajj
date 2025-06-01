<?php

namespace App\Http\Controllers;

use App\Models\Mitra;
use App\Models\Ujroh;
use App\Models\Customer;
use App\Models\Province;
use App\Models\Regency;
use App\Helpers\UploadFile;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\Validator;

class Member_MitraController extends Controller
{
    public function index()
    {
        $title = "Pendaftaran Mitra";
        $loggedInMitra = Auth::guard('mitra')->user();
        $mitraInfo = $loggedInMitra
            ? $loggedInMitra->name . ' (' . $loggedInMitra->code . ')'
            : null;

        return view('pages.mitra.pendaftaran', compact('title', 'mitraInfo'));
    }

    public function show(Mitra $mitra)
    {
        $title = "Detail Mitra";
        return view('pages.mitra.detail-mitra', compact('title', 'mitra'));
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
            'sex.required' => 'Jenis kelamin wajib dipilih',
            'phone.required' => 'Nomor telepon wajib diisi',
            'level.required' => 'Level wajib dipilih',
            'picture_profile.image' => 'File foto profile harus berupa gambar',
            'picture_profile.mimes' => 'Format foto profile harus jpeg, png, atau jpg',
            'picture_profile.max' => 'Ukuran foto profile maksimal 2MB',
            'picture_ktp.image' => 'File foto KTP harus berupa gambar',
            'picture_ktp.mimes' => 'Format foto KTP harus jpeg, png, atau jpg',
            'picture_ktp.max' => 'Ukuran foto KTP maksimal 2MB',
            'province_id.exists' => 'Provinsi tidak valid',
            'regency_id.exists' => 'Kota/Kabupaten tidak valid',
        ];

        $validator = Validator::make($request->all(), [
            'username' => 'required|unique:mitras,username',
            'email' => 'nullable|email|unique:mitras,email',
            'password' => 'required|min:6',
            'name' => 'required',
            'sex' => 'required|in:L,P',
            'phone' => 'required',
            'province_id' => 'nullable|exists:provinces,id',
            'regency_id' => 'nullable|exists:regencies,id',
            'picture_profile' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'picture_ktp' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ], $messages);

        if ($validator->fails()) {
            Alert::error('Error', $validator->errors()->first());
            return back()->withErrors($validator)->withInput();
        }

        try {
            $lastCode = DB::table('mitras')
                ->whereNotNull('code')
                ->orderBy('code', 'desc')
                ->lockForUpdate()
                ->value('code');

            $newCodeNumber = ($lastCode ? intval($lastCode) + 1 : 1);
            $newCode = str_pad($newCodeNumber, 10, '0', STR_PAD_LEFT);

            while (DB::table('mitras')->where('code', $newCode)->exists()) {
                $newCodeNumber++;
                $newCode = str_pad($newCodeNumber, 10, '0', STR_PAD_LEFT);
            }

            $referral_code = strtolower(Str::random(7));
            $picture_profile = null;
            if ($request->hasFile('picture_profile')) {
                $picture_profile = UploadFile::file($request->file('picture_profile'), 'mitra/profile');
            }

            $picture_ktp = null;
            if ($request->hasFile('picture_ktp')) {
                $picture_ktp = UploadFile::file($request->file('picture_ktp'), 'mitra/ktp');
            }

            $loggedInMitra = Auth::guard('mitra')->user();
            $codeMitra = $loggedInMitra->code ?? null;

            Mitra::create([
                'code' => $newCode,
                'username' => $request->username,
                'password' => Hash::make($request->password),
                'referral_code' => $referral_code,
                'level' => 'mitra',
                'code_mitra' => $codeMitra,
                'name' => $request->name,
                'NIK' => $request->NIK,
                'sex' => $request->sex,
                'birth_place' => $request->birth_place,
                'birth_date' => $request->birth_date,
                'address' => $request->address,
                'code_city' => $request->regency_id,
                'code_province' => $request->province_id,
                'phone' => $request->phone,
                'email' => $request->email,
                'bank' => $request->bank,
                'bank_number' => $request->bank_number,
                'bank_name' => $request->bank_name,
                'picture_profile' => $picture_profile,
                'picture_ktp' => $picture_ktp,
                'status' => 'nonactive'
            ]);

            Alert::success('Berhasil', 'Data Mitra berhasil ditambahkan')
                ->persistent(true)
                ->autoClose(5000);
            return redirect()->route('mitra.registration');
        } catch (\Exception $e) {
            Log::error('Mitra Registration Error: ' . $e->getMessage());

            if (isset($picture_profile)) {
                UploadFile::delete('mitra/profile', $picture_profile);
            }
            if (isset($picture_ktp)) {
                UploadFile::delete('mitra/ktp', $picture_ktp);
            }
            Alert::error('Error', 'Terjadi kesalahan pada sistem. Silakan coba lagi.')
                ->persistent(true)
                ->autoClose(5000);
            return back()->withInput();
        }
    }

    public function list(Request $request)
    {
        if ($request->ajax()) {
            $loggedInMitra = Auth::guard('mitra')->user();
            $loggedInCode = $loggedInMitra->code ?? null;
            $filterLevel = $request->get('level');
            $filterStatus = $request->get('status');

            $data = Mitra::query()
                ->select([
                    'id',
                    'code',
                    'name',
                    'email',
                    'phone',
                    'level',
                    'status',
                    'picture_profile'
                ])
                ->where(function ($query) use ($loggedInMitra) {
                    if ($loggedInMitra->level === 'mitra') {
                        $query->where('level', 'mitra')
                            ->where('code_mitra', $loggedInMitra->code);
                    } elseif ($loggedInMitra->level === 'pembina') {
                        $query->whereIn('level', ['mitra', 'pembimbing']);
                    } elseif ($loggedInMitra->level === 'pembimbing') {
                        $query->where('level', 'mitra');
                    }
                });

            if (!empty($filterLevel)) {
                $data->where('level', $filterLevel);
            }

            if (!empty($filterStatus)) {
                $data->where('status', $filterStatus);
            }

            $data->orderBy('name', 'asc');

            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('email', function ($row) {
                    return $row->email ?? '-';
                })
                ->editColumn('phone', function ($row) {
                    return $row->phone ?? '-';
                })
                ->editColumn('level', function ($row) {
                    $badgeClass = 'bg-label-primary';
                    $levelText = ucfirst($row->level);
                    switch ($row->level) {
                        case 'mitra':
                            $badgeClass = 'bg-label-info';
                            break;
                        case 'pembina':
                            $badgeClass = 'bg-label-warning';
                            break;
                        case 'pembimbing':
                            $badgeClass = 'bg-label-success';
                            break;
                        default:
                            $badgeClass = 'bg-label-secondary';
                    }

                    return '<span class="badge rounded-pill ' . $badgeClass . '">' . $levelText . '</span>';
                })
                ->editColumn('status', function ($row) {
                    $class = $row->status === 'active' ? 'bg-label-success' : 'bg-label-danger';
                    $title = $row->status === 'active' ? 'Active' : 'Nonactive';

                    return '<span class="badge rounded-pill ' . $class . '">' . $title . '</span>';
                })
                ->addColumn('full_name', function ($row) {
                    $avatar = $row->picture_profile ?
                        '<img src="' . $row->picture_profile . '" alt="Avatar" class="rounded-circle" width="32">' :
                        '<span class="avatar-initial rounded-circle bg-label-primary">' . strtoupper(substr($row->name ?? 'U', 0, 2)) . '</span>';

                    return '<a href="' . route('mitra.show', $row->id) . '" class="d-flex justify-content-start align-items-center text-body text-decoration-none">
                            <div class="avatar me-2">
                                ' . $avatar . '
                            </div>
                            <div class="d-flex flex-column">
                                <span class="text-truncate fw-medium">' . ($row->name ?? 'Unnamed') . '</span>
                                <small class="text-muted">' . ($row->code ?? '-') . '</small>
                            </div>
                        </a>';
                })
                ->rawColumns(['full_name', 'level', 'status'])
                ->make(true);
        }

        $title = "List Mitra";
        return view('pages.mitra.list', compact('title'));
    }

    public function getParentMitra(Request $request)
    {
        $search = $request->search;

        $mitras = Mitra::where('name', 'like', "%$search%")
            ->orWhere('code', 'like', "%$search%")
            ->whereNull('code_mitra')
            ->get(['id', 'name', 'code']);

        return response()->json($mitras);
    }

    public function genealogy()
    {
        $title = "Bagan Mitra Anda";
        $loggedInMitra = Auth::guard('mitra')->user();

        if (!$loggedInMitra) {
            Alert::error('Error', 'Anda harus login terlebih dahulu.');
            return redirect()->route('login');
        }

        $tree = $loggedInMitra->buildTree();

        return view('pages.mitra.genealogy', compact('tree', 'title'));
    }



    public function getAllDataMitra(Request $request)
    {
        $codeMitra = $request->header('code_mitra');

        $mitra = Mitra::with([
            'category:code,name',
            'cabang:code,name',
            'city:id,name',
            'province:id,name',
            'children' => function ($query) {
                $query->select('id', 'code', 'code_mitra', 'name', 'level');
            }
        ])
            ->where('code_mitra', $codeMitra)
            ->select([
                'id',
                'code',
                'username',
                'referral_code',
                'code_category',
                'code_cabang',
                'code_mitra',
                'level',
                'name',
                'phone',
                'email',
                'picture_profile',
                'status'
            ])
            ->get();

        if ($mitra->isEmpty()) {
            return response()->json(['status' => false, 'message' => 'Data mitra tidak ditemukan', 'data' => null], 404);
        }

        $transformedData = $mitra->map(function ($item) {
            return [
                'id' => $item->id,
                'code' => $item->code,
                'username' => $item->username,
                'referral_code' => $item->referral_code,
                'level' => $item->level,
                'name' => $item->name,
                'phone' => $item->phone,
                'email' => $item->email,
                'picture_profile' => $item->picture_profile,
                'status' => $item->status,
                'category' => $item->category ? ['code' => $item->category->code, 'name' => $item->category->name] : null,
                'cabang' => $item->cabang ? ['code' => $item->cabang->code, 'name' => $item->cabang->name] : null,
                'city' => $item->city ? ['id' => $item->city->id, 'name' => $item->city->name] : null,
                'province' => $item->province ? ['id' => $item->province->id, 'name' => $item->province->name] : null,
                'downline_count' => $item->children->count(),
            ];
        });

        return response()->json(['status' => true, 'message' => 'Data mitra berhasil diambil', 'data' => $transformedData], 200);
    }

    public function storeMitraApi(Request $request)
    {
        $messages = [
            'code_mitra.required' => 'Code mitra tidak ditemukan',
            'username.required' => 'Username wajib diisi',
            'username.unique' => 'Username sudah digunakan',
            'email.email' => 'Format email tidak valid',
            'email.unique' => 'Email sudah digunakan',
            'password.required' => 'Password wajib diisi',
            'password.min' => 'Password minimal 6 karakter',
            'name.required' => 'Nama wajib diisi',
            'sex.required' => 'Jenis kelamin wajib dipilih',
            'phone.required' => 'Nomor telepon wajib diisi',
            'picture_profile.image' => 'File foto profile harus berupa gambar',
            'picture_profile.mimes' => 'Format foto profile harus jpeg, png, atau jpg',
            'picture_profile.max' => 'Ukuran foto profile maksimal 2MB',
            'picture_ktp.image' => 'File foto KTP harus berupa gambar',
            'picture_ktp.mimes' => 'Format foto KTP harus jpeg, png, atau jpg',
            'picture_ktp.max' => 'Ukuran foto KTP maksimal 2MB',
            'province_id.exists' => 'Provinsi tidak valid',
            'regency_id.exists' => 'Kota/Kabupaten tidak valid',
        ];

        $validator = Validator::make($request->all(), [
            'code_mitra' => 'required',
            'username' => 'required|unique:mitras,username',
            'email' => 'nullable|email|unique:mitras,email',
            'password' => 'required|min:6',
            'name' => 'required',
            'sex' => 'required|in:L,P',
            'phone' => 'required',
            'province_id' => 'nullable|exists:provinces,id',
            'regency_id' => 'nullable|exists:regencies,id',
            'picture_profile' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
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

            $lastCode = DB::table('mitras')
                ->whereNotNull('code')
                ->orderBy('code', 'desc')
                ->lockForUpdate()
                ->value('code');

            $newCodeNumber = ($lastCode ? intval($lastCode) + 1 : 1);
            $newCode = str_pad($newCodeNumber, 10, '0', STR_PAD_LEFT);

            while (DB::table('mitras')->where('code', $newCode)->exists()) {
                $newCodeNumber++;
                $newCode = str_pad($newCodeNumber, 10, '0', STR_PAD_LEFT);
            }


            $picture_profile = $request->hasFile('picture_profile') ?
                UploadFile::file($request->file('picture_profile'), 'mitra/profile') : null;

            $picture_ktp = $request->hasFile('picture_ktp') ?
                UploadFile::file($request->file('picture_ktp'), 'mitra/ktp') : null;


            $province = null;
            $regency = null;

            if ($request->province_id) {
                $province = Province::find($request->province_id);
            }

            if ($request->regency_id) {
                $regency = Regency::find($request->regency_id);
            }


            $mitra = Mitra::create([
                'code' => $newCode,
                'username' => $request->username,
                'password' => Hash::make($request->password),
                'referral_code' => strtolower(Str::random(7)),
                'level' => 'mitra',
                'code_mitra' => $request->code_mitra,
                'name' => $request->name,
                'NIK' => $request->NIK,
                'sex' => $request->sex,
                'birth_place' => $request->birth_place,
                'birth_date' => $request->birth_date,
                'address' => $request->address,
                'code_city' => $request->regency_id,
                'code_province' => $request->province_id,
                'phone' => $request->phone,
                'email' => $request->email,
                'bank' => $request->bank,
                'bank_number' => $request->bank_number,
                'bank_name' => $request->bank_name,
                'picture_profile' => $picture_profile,
                'picture_ktp' => $picture_ktp,
                'status' => 'nonactive'
            ]);

            DB::commit();


            $responseData = $mitra->toArray();
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
                'message' => 'Data Mitra berhasil ditambahkan',
                'data' => $responseData
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();

            if (isset($picture_profile)) {
                UploadFile::delete('mitra/profile', $picture_profile);
            }
            if (isset($picture_ktp)) {
                UploadFile::delete('mitra/ktp', $picture_ktp);
            }

            Log::error('Mitra Registration Error: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan pada sistem: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getDetailMitra($id)
    {
        try {
            $mitra = Mitra::with([
                'category:code,name',
                'cabang:code,name',
                'city',
                'province',
                'parent:id,code,name',
                'children:id,code,name,level'
            ])
                ->findOrFail($id);
            Log::info('Mitra Debug Info:', [
                'mitra_id' => $mitra->id,
                'code_city' => $mitra->code_city,
                'code_province' => $mitra->code_province,
                'city_loaded' => $mitra->city ? true : false,
                'province_loaded' => $mitra->province ? true : false,
                'city_data' => $mitra->city,
                'province_data' => $mitra->province,
            ]);
            $transformedData = [
                'id' => $mitra->id,
                'code' => $mitra->code,
                'username' => $mitra->username,
                'referral_code' => $mitra->referral_code,
                'name' => $mitra->name,
                'phone' => $mitra->phone,
                'email' => $mitra->email,
                'level' => $mitra->level,
                'status' => $mitra->status,
                'address' => $mitra->address,
                'NIK' => $mitra->NIK,
                'sex' => $mitra->sex,
                'birth_place' => $mitra->birth_place,
                'birth_date' => $mitra->birth_date,
                'picture_profile' => $mitra->picture_profile,
                'picture_ktp' => $mitra->picture_ktp,
                'code_mitra' => $mitra->code_mitra,
                'bank' => $mitra->bank,
                'bank_number' => $mitra->bank_number,
                'bank_name' => $mitra->bank_name,
                'category' => $mitra->category ? [
                    'code' => $mitra->category->code,
                    'name' => $mitra->category->name
                ] : null,
                'cabang' => $mitra->cabang ? [
                    'code' => $mitra->cabang->code,
                    'name' => $mitra->cabang->name
                ] : null,
                'city' => $mitra->city ? [
                    'id' => $mitra->city->id,
                    'name' => $mitra->city->name
                ] : null,
                'province' => $mitra->province ? [
                    'id' => $mitra->province->id,
                    'name' => $mitra->province->name
                ] : null,
                'parent_mitra' => $mitra->parent ? [
                    'id' => $mitra->parent->id,
                    'code' => $mitra->parent->code,
                    'name' => $mitra->parent->name
                ] : null,
                'downlines' => $mitra->children->map(function ($child) {
                    return [
                        'id' => $child->id,
                        'code' => $child->code,
                        'name' => $child->name,
                        'level' => $child->level
                    ];
                }),
                'downline_count' => $mitra->children->count(),
                'created_at' => $mitra->created_at,
                'updated_at' => $mitra->updated_at
            ];

            return response()->json([
                'status' => true,
                'message' => 'Detail mitra berhasil diambil',
                'data' => $transformedData
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Data mitra tidak ditemukan: ' . $e->getMessage(),
                'data' => null
            ], 404);
        }
    }


    public function updateMitraApi(Request $request, $id)
    {
        try {
            $mitra = Mitra::findOrFail($id);

            $messages = [
                'username.unique' => 'Username sudah digunakan',
                'email.email' => 'Format email tidak valid',
                'email.unique' => 'Email sudah digunakan',
                'name.required' => 'Nama wajib diisi',
                'sex.required' => 'Jenis kelamin wajib dipilih',
                'phone.required' => 'Nomor telepon wajib diisi',
                'picture_profile.image' => 'File foto profile harus berupa gambar',
                'picture_profile.mimes' => 'Format foto profile harus jpeg, png, atau jpg',
                'picture_profile.max' => 'Ukuran foto profile maksimal 2MB',
                'picture_ktp.image' => 'File foto KTP harus berupa gambar',
                'picture_ktp.mimes' => 'Format foto KTP harus jpeg, png, atau jpg',
                'picture_ktp.max' => 'Ukuran foto KTP maksimal 2MB',
                'province_id.exists' => 'Provinsi tidak valid',
                'regency_id.exists' => 'Kota/Kabupaten tidak valid',
            ];

            $validator = Validator::make($request->all(), [
                'username' => 'nullable|unique:mitras,username,' . $mitra->id,
                'email' => 'nullable|email|unique:mitras,email,' . $mitra->id,
                'name' => 'required',
                'sex' => 'nullable|in:L,P',
                'phone' => 'required',
                'province_id' => 'nullable|exists:provinces,id',
                'regency_id' => 'nullable|exists:regencies,id',
                'picture_profile' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
                'picture_ktp' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            ], $messages);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => $validator->errors()->first()
                ], 400);
            }

            DB::beginTransaction();


            $picture_profile = $mitra->picture_profile;
            if ($request->hasFile('picture_profile')) {
                if ($mitra->picture_profile) {
                    UploadFile::delete('mitra/profile', $mitra->picture_profile);
                }
                $picture_profile = UploadFile::file($request->file('picture_profile'), 'mitra/profile');
            }

            $picture_ktp = $mitra->picture_ktp;
            if ($request->hasFile('picture_ktp')) {
                if ($mitra->picture_ktp) {
                    UploadFile::delete('mitra/ktp', $mitra->picture_ktp);
                }
                $picture_ktp = UploadFile::file($request->file('picture_ktp'), 'mitra/ktp');
            }


            $mitra->update([
                'username' => $request->username ?? $mitra->username,
                'name' => $request->name,
                'phone' => $request->phone,
                'email' => $request->email,
                'NIK' => $request->NIK,
                'sex' => $request->sex,
                'birth_place' => $request->birth_place,
                'birth_date' => $request->birth_date,
                'address' => $request->address,
                'code_city' => $request->regency_id,
                'code_province' => $request->province_id,
                'bank' => $request->bank,
                'bank_number' => $request->bank_number,
                'bank_name' => $request->bank_name,
                'picture_profile' => $picture_profile,
                'picture_ktp' => $picture_ktp,
            ]);

            DB::commit();


            $updatedMitra = Mitra::with([
                'category:code,name',
                'cabang:code,name',
                'city:id,name',
                'province:id,name'
            ])->findOrFail($id);

            $transformedData = [
                'id' => $updatedMitra->id,
                'code' => $updatedMitra->code,
                'username' => $updatedMitra->username,
                'name' => $updatedMitra->name,
                'phone' => $updatedMitra->phone,
                'email' => $updatedMitra->email,
                'level' => $updatedMitra->level,
                'status' => $updatedMitra->status,
                'address' => $updatedMitra->address,
                'NIK' => $updatedMitra->NIK,
                'sex' => $updatedMitra->sex,
                'birth_place' => $updatedMitra->birth_place,
                'birth_date' => $updatedMitra->birth_date,
                'picture_profile' => $updatedMitra->picture_profile,
                'picture_ktp' => $updatedMitra->picture_ktp,
                'bank' => $updatedMitra->bank,
                'bank_number' => $updatedMitra->bank_number,
                'bank_name' => $updatedMitra->bank_name,
                'category' => $updatedMitra->category ? ['code' => $updatedMitra->category->code, 'name' => $updatedMitra->category->name] : null,
                'cabang' => $updatedMitra->cabang ? ['code' => $updatedMitra->cabang->code, 'name' => $updatedMitra->cabang->name] : null,
                'city' => $updatedMitra->city ? ['id' => $updatedMitra->city->id, 'name' => $updatedMitra->city->name] : null,
                'province' => $updatedMitra->province ? ['id' => $updatedMitra->province->id, 'name' => $updatedMitra->province->name] : null,
            ];

            return response()->json([
                'status' => 'success',
                'message' => 'Data Mitra berhasil diupdate',
                'data' => $transformedData
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Mitra Update Error: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan pada sistem: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getMitraCustomersWithBonus($mitraId)
    {
        try {
            $codeMitra = request()->header('code_mitra');

            if (!$codeMitra) {
                return response()->json([
                    'status' => false,
                    'message' => 'Code mitra tidak ditemukan di header'
                ], 401);
            }

            $loggedInMitra = Mitra::where('code', $codeMitra)->first();

            if (!$loggedInMitra) {
                return response()->json([
                    'status' => false,
                    'message' => 'Mitra dengan code tersebut tidak ditemukan'
                ], 401);
            }
            $targetMitra = Mitra::findOrFail($mitraId);
            $customers = Customer::with([
                'category:code,name',
                'cabang:code,name',
                'city:id,name',
                'province:id,name',
                'program:code,name',
                'jamaah'
            ])
                ->where('code_mitra', $targetMitra->code)
                ->select([
                    'id',
                    'code',
                    'name',
                    'phone',
                    'email',
                    'status',
                    'status_prospek',
                    'status_jamaah',
                    'status_alumni',
                    'address',
                    'code_category',
                    'code_cabang',
                    'code_city',
                    'code_province',
                    'code_program',
                    'created_at'
                ])
                ->orderBy('name', 'asc')
                ->get();

            $customersWithBonus = $customers->map(function ($customer) {

                $totalBonusDebit = Ujroh::where('code_customer', $customer->code)
                    ->where('status', 'debit')
                    ->sum('value') ?? 0;

                $totalBonusCredit = Ujroh::where('code_customer', $customer->code)
                    ->where('status', 'credit')
                    ->sum('value') ?? 0;

                $bonusBalance = $totalBonusDebit - $totalBonusCredit;

                return [
                    'id' => $customer->id,
                    'code' => $customer->code,
                    'name' => $customer->name,
                    'phone' => $customer->phone,
                    'email' => $customer->email,
                    'status' => $customer->status,
                    'status_prospek' => $customer->status_prospek,
                    'status_jamaah' => $customer->status_jamaah,
                    'status_alumni' => $customer->status_alumni,
                    'address' => $customer->address,
                    'created_at' => $customer->created_at,
                    'category' => $customer->category ? [
                        'code' => $customer->category->code,
                        'name' => $customer->category->name
                    ] : null,
                    'cabang' => $customer->cabang ? [
                        'code' => $customer->cabang->code,
                        'name' => $customer->cabang->name
                    ] : null,
                    'city' => $customer->city ? [
                        'id' => $customer->city->id,
                        'name' => $customer->city->name
                    ] : null,
                    'province' => $customer->province ? [
                        'id' => $customer->province->id,
                        'name' => $customer->province->name
                    ] : null,
                    'program' => $customer->program ? [
                        'code' => $customer->program->code,
                        'name' => $customer->program->name
                    ] : null,
                    'jamaah' => $customer->jamaah ? [
                        'code' => $customer->jamaah->code,
                        'status_payment' => $customer->jamaah->status_payment ?? null,
                        'status_berangkat' => $customer->jamaah->status_berangkat ?? null
                    ] : null,
                    'bonus_info' => [
                        'total_debit' => $totalBonusDebit,
                        'total_credit' => $totalBonusCredit,
                        'balance' => $bonusBalance
                    ]
                ];
            });

            $totalCustomers = $customers->count();

            if ($totalCustomers > 0) {
                $customerCodes = $customers->pluck('code');
                $totalBonusDebit = Ujroh::whereIn('code_customer', $customerCodes)
                    ->where('status', 'debit')
                    ->sum('value') ?? 0;
                $totalBonusCredit = Ujroh::whereIn('code_customer', $customerCodes)
                    ->where('status', 'credit')
                    ->sum('value') ?? 0;
                $totalBonusBalance = $totalBonusDebit - $totalBonusCredit;
            } else {
                $totalBonusDebit = 0;
                $totalBonusCredit = 0;
                $totalBonusBalance = 0;
            }

            $statistics = [
                'total_customers' => $totalCustomers,
                'customers_by_status' => [
                    'prospek' => $customers->where('status', 'prospek')->count(),
                    'jamaah' => $customers->where('status', 'jamaah')->count(),
                    'alumni' => $customers->where('status', 'alumni')->count()
                ],
                'bonus_summary' => [
                    'total_debit' => $totalBonusDebit,
                    'total_credit' => $totalBonusCredit,
                    'total_balance' => $totalBonusBalance,
                    'average_bonus_per_customer' => $totalCustomers > 0 ?
                        round($totalBonusBalance / $totalCustomers, 2) : 0
                ]
            ];

            return response()->json([
                'status' => true,
                'message' => 'Data customer dengan bonus berhasil diambil',
                'data' => [
                    'logged_in_mitra' => [
                        'code' => $loggedInMitra->code,
                        'name' => $loggedInMitra->name
                    ],
                    'mitra_info' => [
                        'id' => $targetMitra->id,
                        'code' => $targetMitra->code,
                        'name' => $targetMitra->name,
                        'level' => $targetMitra->level,
                        'status' => $targetMitra->status
                    ],
                    'customers' => $customersWithBonus,
                    'statistics' => $statistics
                ]
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error('Mitra target not found: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Mitra dengan ID tersebut tidak ditemukan'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Error fetching mitra customers with bonus: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan pada sistem: ' . $e->getMessage()
            ], 500);
        }
    }
    private function getAllDownlines($mitraCode, $result = [])
    {
        try {
            if (empty($mitraCode)) {
                return $result;
            }

            $directDownlines = Mitra::where('code_mitra', $mitraCode)->pluck('code')->toArray();

            foreach ($directDownlines as $downlineCode) {
                if (!in_array($downlineCode, $result)) {
                    $result[] = $downlineCode;
                    $result = $this->getAllDownlines($downlineCode, $result);
                }
            }

            return array_unique($result);
        } catch (\Exception $e) {
            Log::error('Error getting downlines for mitra code: ' . $mitraCode . ' - ' . $e->getMessage());
            return $result;
        }
    }
}