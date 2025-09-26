<?php

namespace App\Http\Controllers;

use App\Models\Mitra;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\Validator;

class Member_AuthController extends Controller
{
    public function index()
    {
        $title = "Login Member";
        return view('auth.login', compact('title'));
    }

    public function login(Request $request)
    {
        try {
            $field = filter_var($request->input('email-username'), FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

            $credentials = [
                $field => $request->input('email-username'),
                'password' => $request->password
            ];

            $mitra = Mitra::where($field, $request->input('email-username'))->first();

            if (!$mitra) {
                Alert::error('Login Gagal', $field === 'email' ? 'Email tidak terdaftar' : 'Username tidak terdaftar');
                return redirect()->back()->withInput($request->except('password'));
            }

            if ($mitra->status !== 'active') {
                Alert::error('Login Gagal', 'Akun Anda tidak aktif. Silahkan hubungi admin.');
                return redirect()->back()->withInput($request->except('password'));
            }

            if (Auth::guard('mitra')->attempt($credentials)) {
                $request->session()->regenerate();
                Alert::success('Login Berhasil', 'Selamat datang ' . Auth::guard('mitra')->user()->name);
                return redirect()->intended(route('member.dashboard'));
            }

            Alert::error('Login Gagal', 'Password tidak sesuai');
            return redirect()->back()->withInput($request->except('password'));
        } catch (\Exception $e) {
            Alert::error('Error', 'Terjadi kesalahan sistem. Silahkan coba lagi.');
            return redirect()->back()->withInput($request->except('password'));
        }
    }

    public function logout(Request $request)
    {
        Auth::guard('mitra')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        Alert::success('Logout Berhasil', 'Anda telah keluar dari sistem');
        return redirect()->route('root');
    }

    public function loginApi(Request $request)
    {
        try {
            $field = filter_var($request->input('email-username'), FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

            $credentials = [
                $field => $request->input('email-username'),
                'password' => $request->password
            ];

            $mitra = Mitra::where($field, $request->input('email-username'))->first();

            if (!$mitra) {
                return response()->json(['error' => $field === 'email' ? 'Email tidak terdaftar' : 'Username tidak terdaftar'], 401);
            }

            if ($mitra->status !== 'active') {
                return response()->json(['error' => 'Akun Anda tidak aktif. Silahkan hubungi admin.'], 401);
            }

            if (Auth::guard('mitra')->attempt($credentials)) {
                $request->session()->regenerate();
                return response()->json(['message' => 'Login berhasil', 'user' => Auth::guard('mitra')->user()]);
            }

            return response()->json(['error' => 'Password tidak sesuai'], 401);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Terjadi kesalahan sistem. Silahkan coba lagi.'], 500);
        }
    }

    public function registerApi(Request $request)
    {
        $request->validate([
            'username' => 'required|unique:mitras',
            'password' => 'required|min:8',
            'name' => 'required',
            'phone' => 'required',
            'sex' => 'required|in:L,P',
            'code_mitra' => 'nullable|exists:mitras,code',
        ]);

        $lastMitra = Mitra::orderBy('code', 'desc')->first();
        $lastCode = $lastMitra ? $lastMitra->code : '0000000000';
        $newCode = $this->generateNewCode($lastCode);
        $referralMitra = null;
        if ($request->filled('code_mitra')) {
            $referralMitra = Mitra::where('code', $request->code_mitra)->first();

            if (!$referralMitra) {
                return response()->json([
                    'message' => 'Code mitra tidak valid'
                ], 422);
            }
        }
        $mitra = new Mitra();
        $mitra->code = $newCode;
        $mitra->username = $request->username;
        $mitra->password = Hash::make($request->password);
        $mitra->referral_code = $this->generateReferralCode();
        $mitra->code_category = null;
        $mitra->code_cabang = null;
        $mitra->code_mitra = $request->filled('code_mitra') ? $request->code_mitra : null;
        $mitra->level = 'mitra';
        $mitra->name = $request->name;
        $mitra->NIK = null;
        $mitra->sex = $request->sex;
        $mitra->birth_place = null;
        $mitra->birth_date = null;
        $mitra->address = null;
        $mitra->code_city = null;
        $mitra->code_province = null;
        $mitra->phone = $request->phone;
        $mitra->email = null;
        $mitra->bank = null;
        $mitra->bank_number = null;
        $mitra->bank_name = null;
        $mitra->picture_profile = null;
        $mitra->picture_ktp = null;
        $mitra->status = 'nonactive';
        $mitra->save();
        return response()->json([
            'message' => 'Registrasi mitra berhasil',
            'data' => [
                'code' => $mitra->code,
                'username' => $mitra->username,
                'name' => $mitra->name,
                'code_mitra' => $referralMitra ? $referralMitra->name : null
            ]
        ], 201);
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|unique:mitras,username',
            'password' => 'required|min:8',
            'name' => 'required',
            'sex' => 'required|in:L,P',
            'phone' => 'required|unique:mitras,phone',
            'code_mitra' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors()
            ], 422);
        }

        $referralMitra = null;
        $codeMitraValue = null;

        if ($request->filled('code_mitra')) {
            $referralMitra = Mitra::where('code', $request->code_mitra)->first();

            if (!$referralMitra) {
                return response()->json([
                    'message' => 'Code mitra tidak valid atau tidak ditemukan',
                    'status' => 'error',
                ], 422);
            }

            $codeMitraValue = $request->code_mitra;
        }

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

        $mitra = Mitra::create([
            'code' => $newCode,
            'username' => $request->username,
            'password' => bcrypt($request->password),
            'referral_code' => strtolower(Str::random(7)),
            'code_mitra' => $codeMitraValue, 
            'level' => 'mitra',
            'name' => $request->name,
            'sex' => $request->sex,
            'phone' => $request->phone,
            'status' => 'nonactive',
        ]);

        return response()->json([
            'message' => 'Mitra berhasil didaftarkan',
            'status' => 'success',
            'data' => [
                'code' => $mitra->code,
                'username' => $mitra->username,
                'name' => $mitra->name,
                'code_mitra' => $mitra->code_mitra,
                'referral_by' => $referralMitra ? $referralMitra->name : null,
            ]
        ], 201);
    }

    private function generateNewCode($lastCode)
    {
        $numericPart = substr($lastCode, -10);
        $newNumericPart = str_pad((int) $numericPart + 1, 10, '0', STR_PAD_LEFT);
        return $newNumericPart;
    }

    private function generateReferralCode()
    {
        $referralCode = '';
        do {
            $referralCode = Str::random(8);
        } while (Mitra::where('referral_code', $referralCode)->exists());
        return $referralCode;
    }
}
