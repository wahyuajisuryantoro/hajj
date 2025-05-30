<?php

namespace App\Http\Controllers;

use App\Models\Regency;
use App\Models\Province;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RegionController extends Controller
{
    public function getProvinces()
    {
        try {
            $provinces = Province::select('id', 'name')->orderBy('name', 'asc')->get();

            return response()->json([
                'status' => 'success',
                'message' => 'Data provinsi berhasil diambil',
                'data' => $provinces
            ], 200);
        } catch (\Exception $e) {
            Log::error('Get Provinces Error: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan pada sistem: ' . $e->getMessage()
            ], 500);
        }
    }
    public function getRegenciesByProvince($provinceId)
    {
        try {
            $regencies = Regency::where('province_id', $provinceId)
                ->select('id', 'name')
                ->orderBy('name', 'asc')
                ->get();

            return response()->json([
                'status' => 'success',
                'message' => 'Data kota/kabupaten berhasil diambil',
                'data' => $regencies
            ], 200);
        } catch (\Exception $e) {
            Log::error('Get Regencies Error: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan pada sistem: ' . $e->getMessage()
            ], 500);
        }
    }
}
