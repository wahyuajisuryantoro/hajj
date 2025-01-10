<?php

namespace App\Http\Controllers;

use App\Helpers\UploadFile;
use App\Models\Mitra;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use RealRashid\SweetAlert\Facades\Alert;
use Yajra\DataTables\DataTables;

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
        ];


        $validator = Validator::make($request->all(), [
            'username' => 'required|unique:mitras,username',
            'email' => 'nullable|email|unique:mitras,email',
            'password' => 'required|min:6',
            'name' => 'required',
            'sex' => 'required|in:L,P',
            'phone' => 'required',
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
                'code_city' => $request->code_city,
                'code_province' => $request->code_province,
                'phone' => $request->phone,
                'email' => $request->email,
                'bank' => $request->bank,
                'bank_number' => $request->bank_number,
                'bank_name' => $request->bank_name,
                'picture_profile' => $picture_profile,
                'picture_ktp' => $picture_ktp,
                'status' => 'active'
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

            // Ambil parameter filter dari request
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

            // Terapkan filter Level jika ada
            if (!empty($filterLevel)) {
                $data->where('level', $filterLevel);
            }

            // Terapkan filter Status jika ada
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

                    // Anda dapat menyesuaikan warna badge berdasarkan level
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

    private function buildMitraTree($mitra)
    {
        $tree = [
            'id' => $mitra->id,
            'name' => $mitra->name,
            'email' => $mitra->email,
            'picture_profile' => $mitra->picture_profile,
            'children' => []
        ];

        $children = Mitra::where('code_mitra', $mitra->code)->get();
        foreach ($children as $child) {
            $tree['children'][] = $this->buildMitraTree($child);
        }

        return $tree;
    }
}
