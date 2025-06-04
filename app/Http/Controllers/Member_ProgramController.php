<?php

namespace App\Http\Controllers;

use App\Models\Program;
use App\Models\ProgramCategory;
use Illuminate\Http\Request;

class Member_ProgramController extends Controller
{
    public function index(Request $request)
    {
        $title = 'Daftar Program';
        $query = Program::query();

        // Hanya filter berdasarkan status active, tidak filter sisa_kursi
        $query->where('status', 'active');

        if ($request->filled('category')) {
            $query->where('code_category', $request->category);
        }

        if ($request->filled('hide_past') && $request->hide_past == '1') {
            $query->where('tanggal_berangkat', '>=', now());
        }

        $programs = $query->paginate(9);
        $categories = ProgramCategory::all();

        return view('pages.program.index', compact('title', 'programs', 'categories'));
    }

    public function show($code)
    {
        $title = 'Detail Program';
        $program = Program::where('code', $code)->firstOrFail();
        return view('pages.program.show', compact('title', 'program'));
    }

    public function getPrograms(Request $request)
    {
        try {
            $query = Program::query();
            
            // Hanya filter berdasarkan status active
            // Hapus filter sisa_kursi > 0 agar program dengan sisa kursi 0 tetap muncul
            $query->where('status', 'active');

            if ($request->filled('category')) {
                $query->where('code_category', $request->category);
            }

            if ($request->filled('hide_past') && $request->hide_past == '1') {
                $query->where('tanggal_berangkat', '>=', now());
            }

            // Tambahan: Urutkan berdasarkan ketersediaan kursi dan tanggal
            // Program dengan kursi tersedia ditampilkan terlebih dahulu
            $query->orderByRaw('CASE WHEN sisa_kursi > 0 THEN 0 ELSE 1 END')
                  ->orderBy('tanggal_berangkat', 'asc');

            $limit = $request->input('limit', 10);
            $programs = $query->paginate($limit);

            // Tambahkan informasi status untuk setiap program
            $programsWithStatus = $programs->items();
            foreach ($programsWithStatus as $program) {
                $program->is_available = $program->sisa_kursi > 0;
                $program->status_text = $program->sisa_kursi > 0 ? 'Available' : 'Sold Out';
            }

            return response()->json([
                'status' => 'success',
                'data' => $programsWithStatus,
                'meta' => [
                    'current_page' => $programs->currentPage(),
                    'last_page' => $programs->lastPage(),
                    'per_page' => $programs->perPage(),
                    'total' => $programs->total(),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch programs',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getProgramDetail($code)
    {
        try {
            $program = Program::where('code', $code)
                             ->where('status', 'active') // Pastikan program masih active
                             ->firstOrFail();

            // Tambahkan informasi status ketersediaan
            $program->is_available = $program->sisa_kursi > 0;
            $program->status_text = $program->sisa_kursi > 0 ? 'Available' : 'Sold Out';

            return response()->json([
                'status' => 'success',
                'data' => $program
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Program not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    // Method tambahan untuk mendapatkan hanya program yang tersedia
    public function getAvailablePrograms(Request $request)
    {
        try {
            $query = Program::query();
            
            $query->where('status', 'active')
                  ->where('sisa_kursi', '>', 0);

            if ($request->filled('category')) {
                $query->where('code_category', $request->category);
            }

            if ($request->filled('hide_past') && $request->hide_past == '1') {
                $query->where('tanggal_berangkat', '>=', now());
            }

            $query->orderBy('tanggal_berangkat', 'asc');

            $limit = $request->input('limit', 10);
            $programs = $query->paginate($limit);

            return response()->json([
                'status' => 'success',
                'data' => $programs->items(),
                'meta' => [
                    'current_page' => $programs->currentPage(),
                    'last_page' => $programs->lastPage(),
                    'per_page' => $programs->perPage(),
                    'total' => $programs->total(),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch available programs',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}