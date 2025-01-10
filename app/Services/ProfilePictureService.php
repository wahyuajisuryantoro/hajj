<?php

namespace App\Services;

use App\Models\Mitra;
use App\Helpers\UploadFile;
use Illuminate\Support\Facades\Log;

class ProfilePictureService
{
    public function uploadProfilePicture($file, Mitra $mitra)
    {
        Log::info('Handling profile picture upload for user ID: ' . $mitra->id);

        try {
            // Upload foto profil baru menggunakan helper
            $filePath = UploadFile::file($file, 'profile_pictures');
            Log::debug('Uploaded file path:', ['filePath' => $filePath]);

            if ($mitra->picture_profile) {
                Log::info('Existing profile picture found, attempting to delete old file for user ID: ' . $mitra->id);
                $parsedUrl = parse_url($mitra->picture_profile);
                $path = ltrim($parsedUrl['path'], '/');
                $pathParts = explode('/', $path);
                
                if(count($pathParts) >= 3){
                    $pathFolder = $pathParts[1];
                    $filenameWithExt = $pathParts[2]; // 'filename.jpg'
                    $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME); // 'filename'
                    Log::debug('Deleting old profile picture:', ['pathFolder' => $pathFolder, 'filename' => $filename]);
                    UploadFile::delete($pathFolder, $filename);
                    Log::info('Old profile picture deleted for user ID: ' . $mitra->id);
                } else {
                    Log::warning('Unexpected path format for existing profile picture:', ['path' => $path]);
                }
            }

            // Memperbarui URL foto profil di database
            $mitra->picture_profile = $filePath;
            $mitra->save();
            Log::info('Profile picture updated for user ID: ' . $mitra->id);

        } catch (\Exception $e) {
            Log::error('Error uploading profile picture for user ID: ' . $mitra->id, [
                'error_message' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString()
            ]);

            throw $e;
        }
    }
}
