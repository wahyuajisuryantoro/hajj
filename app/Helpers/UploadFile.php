<?php

namespace App\Helpers;

class UploadFile {

    public static function path($path){
        return pathinfo($path, PATHINFO_FILENAME);
    }

    // PARAMETER
    // $request = file yang disimpan ($request->file('nama'))
    // $column   = nama kolom didatabase
    // $path    = lokasi file akan disimpan
    // $datas   = array insert data
    public static function file($request,$path){

        if($request == NULL){
            return NULL;
        }

        // Ambil file
        $files = $request->getRealPath();

        // Mengubah nama foto
        $newFilename = str_replace(' ', '_', $files);
        $public_id = date('Y-m-d_His').'_'.$newFilename;

        // Mengecek ukuran foto
        $size = $request->getSize();
        $transformation = [
            'quality' => 'auto',
            'fetch_format' => 'auto'
        ];

        $result = cloudinary()->upload($files, [
            "public_id" => self::path($public_id),
            "folder"    => env('CLOUDINARY_PROJECT') . '/' . $path,
        ])->getSecurePath();

        return $result;
    }

    public static function delete($path, $id)
    {
        $public_id = env('CLOUDINARY_PROJECT') . '/' . $path . '/' . self::path($id);
        cloudinary()->destroy($public_id);
    }

    // public static function check($file,$table,$path){

    //     if(!$file){
    //         static::file($file,$path);

    //         $data_lama = DB::table($table);
    //     }


    //     if(!empty($request->foto)){
    //         $datas = UploadFile::file($request->file('foto'),'foto','storage/program',$datas);

    //         $data = Layanan::find($request->id);
    //         if(isset($data)){
    //             UploadFile::delete('storage/program',$data->foto);
    //         }
    //     }
    // }
}

?>