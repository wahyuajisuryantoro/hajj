<?php

namespace App\Http\Controllers;

use App\Models\Mitra;
use App\Helpers\UploadFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Services\ProfilePictureService;
use RealRashid\SweetAlert\Facades\Alert;

class Member_AccountController extends Controller
{

  
    public function settings()
    {
        $title = "Pengaturan Akun";
        $mitra = Auth::guard('mitra')->user();
        $banks = $this->getBankList();
        return view('pages.account.content.account', compact('title', 'mitra', 'banks'));
    }

    public function info()
    {
        $title = "Informasi Akun";
        return view('pages.account.info', compact('title'));
    }

    public function updatePasswordIndex(){
        $title = "Update Password";
        $mitra = Auth::guard('mitra')->user();
        return view('pages.account.content.update-password', compact('title', 'mitra'));
    }

   

    public function updateProfile(Request $request)
    {
        Log::info('Update Profile initiated by user ID: ' . Auth::guard('mitra')->id());
    
        try {
            $mitra = Auth::guard('mitra')->user();
            Log::debug('Authenticated Mitra:', ['mitra_id' => $mitra->id]);
    
            // Validasi input (tanpa picture_profile)
            Log::info('Validating input for user ID: ' . $mitra->id);
            $request->validate([
                'name' => 'required|string|max:255',
                'phone' => 'required|string|max:20',
                'email' => 'required|email|max:255|unique:mitras,email,' . $mitra->id,
                'birth_place' => 'required|string|max:100',
                'birth_date' => 'required|date',
                'address' => 'required|string',
            ]);
            Log::info('Validation passed for user ID: ' . $mitra->id);
    
            // Update informasi dasar
            Log::info('Updating basic information for user ID: ' . $mitra->id);
            $mitra->update([
                'name' => $request->name,
                'phone' => $request->phone,
                'email' => $request->email,
                'birth_place' => $request->birth_place,
                'birth_date' => $request->birth_date,
                'address' => $request->address,
            ]);
    
            // Jika foto profil diubah, panggil metode untuk mengubah foto
            if ($request->hasFile('picture_profile')) {
                // Memanggil metode upload foto profil (asumsi sudah dipisahkan)
                $this->profilePictureService->uploadProfilePicture($request->file('picture_profile'), $mitra);
            }
    
            // Menambahkan SweetAlert success setelah update berhasil
            Alert::success('Success', 'Your profile has been updated successfully!');
            Log::info('Profile update completed for user ID: ' . $mitra->id);
    
            // Redirect kembali dengan status sukses
            return redirect()->back();
            
        } catch (\Exception $e) {
            // Menambahkan SweetAlert error jika ada kesalahan
            Log::error('Error updating profile: ' . $e->getMessage());
            Alert::error('Error', 'An error occurred while updating your profile.');
            
            // Redirect kembali dengan status error
            return redirect()->back();
        }
    }

    protected $profilePictureService;

    public function __construct(ProfilePictureService $profilePictureService)
    {
        $this->profilePictureService = $profilePictureService;
    }
    public function updateProfilePicture(Request $request)
    {
        Log::info('Update Profile Picture initiated by user ID: ' . Auth::guard('mitra')->id());
    
        try {
            $mitra = Auth::guard('mitra')->user();
    
            if (!$mitra) {
                Log::error('Authenticated user is null.');
                return response()->json([
                    'success' => false,
                    'message' => 'Pengguna tidak terautentikasi.'
                ], 401);
            }
    
            Log::debug('Authenticated Mitra:', ['mitra_id' => $mitra->id]);
    
            // Validasi input khusus foto profil
            Log::info('Validating profile picture for user ID: ' . $mitra->id);
            $request->validate([
                'picture_profile' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            ]);
            Log::info('Validation passed for profile picture of user ID: ' . $mitra->id);
    
            // Menangani unggahan foto profil menggunakan service
            $this->profilePictureService->uploadProfilePicture($request->file('picture_profile'), $mitra);
    
            Log::info('Profile picture update successful for user ID: ' . $mitra->id);
    
            return response()->json([
                'success' => true,
                'message' => 'Foto profil berhasil diperbarui',
                'data' => $mitra
            ]);
    
        } catch (\Exception $e) {
            // Menangani error
            Log::error('Error updating profile picture for user ID: ' . Auth::guard('mitra')->id(), [
                'error_message' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString()
            ]);
    
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }


    public function editBankAccount()
    {
        $title = "Edit Akun Bank";
        $mitra = Auth::guard('mitra')->user();
        return view('pages.account.content.bank', compact('title','mitra'));
    }

    public function updateBankAccount(Request $request)
    {
        try {
            $mitra = Auth::guard('mitra')->user();

            // Validasi input
            $request->validate([
                'bank' => 'required|string|max:50',
                'bank_number' => 'required|string|max:20',
                'bank_name' => 'required|string|max:255'
            ]);

            // Update informasi bank
            $mitra->update([
                'bank' => $request->bank,
                'bank_number' => $request->bank_number,
                'bank_name' => $request->bank_name
            ]);

            // Flash message success
            return redirect()->route('account.edit-bank')->with('success_bank', 'Informasi bank berhasil diperbarui.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            // Redirect kembali dengan error validation
            return redirect()->route('account.edit-bank')
                             ->withErrors($e->errors())
                             ->withInput()
                             ->with('error_bank', 'Terjadi kesalahan validasi.');

        } catch (\Exception $e) {
            // Log error dan redirect dengan error message
            Log::error('Error updating bank account for user ID: ' . Auth::guard('mitra')->id(), [
                'error_message' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('account.edit-bank')->with('error_bank', 'Terjadi kesalahan saat memperbarui informasi bank.');
        }
    }

    public function getBankAccount()
    {
        try {
            $mitra = Auth::guard('mitra')->user();
            
            return response()->json([
                'success' => true,
                'data' => [
                    'bank' => $mitra->bank,
                    'bank_number' => $mitra->bank_number,
                    'bank_name' => $mitra->bank_name
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    protected function getBankList()
    {
        return [
            'BSI' => 'Bank Syariah Indonesia',
            'BNI' => 'Bank Negara Indonesia',
            'BRI' => 'Bank Rakyat Indonesia',
            'MANDIRI' => 'Bank Mandiri',
            'BCA' => 'Bank Central Asia',
            'CIMB' => 'CIMB Niaga',
            'PERMATA' => 'Bank Permata',
            'MUAMALAT' => 'Bank Muamalat',
            'MEGA' => 'Bank Mega'
        ];
    }

    public function updatePassword(Request $request)
    {
        try {
            $mitra = Auth::guard('mitra')->user();

            // Validasi input
            $request->validate([
                'current_password' => 'required|string',
                'new_password' => 'required|string|min:6|different:current_password',
                'confirm_password' => 'required|string|same:new_password'
            ]);

            // Cek apakah current_password sesuai dengan password saat ini
            if (!Hash::check($request->current_password, $mitra->password)) {
                // Jika password salah
                Alert::error('Gagal', 'Password saat ini tidak sesuai');
                return redirect()->back()->withInput();
            }

            // Update password baru
            $mitra->update([
                'password' => Hash::make($request->new_password)
            ]);

            // Jika berhasil
            Alert::success('Berhasil', 'Password berhasil diperbarui');
            return redirect()->back();

        } catch (\Illuminate\Validation\ValidationException $e) {
            // Menangani error validasi
            Alert::error('Gagal', 'Silakan perbaiki kesalahan pada form.');
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            // Menangani error lainnya
            Alert::error('Terjadi Kesalahan', 'Gagal memperbarui password');
            return redirect()->back()->withInput();
        }
    }

   
    
    

    public function deactivateAccount(Request $request)
    {
        try {
            $mitra = Auth::guard('mitra')->user();

            $request->validate([
                'confirmation' => 'required|accepted'
            ]);

            // Deactivate account
            $mitra->update([
                'status' => 'nonactive'
            ]);

            Auth::guard('mitra')->logout();

            return response()->json([
                'success' => true,
                'message' => 'Akun berhasil dinonaktifkan'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}