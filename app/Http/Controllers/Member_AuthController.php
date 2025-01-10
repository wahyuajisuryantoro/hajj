<?php

namespace App\Http\Controllers;

use App\Models\Mitra;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use RealRashid\SweetAlert\Facades\Alert;

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

}
