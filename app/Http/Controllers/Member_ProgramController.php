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

     // Endpoint baru untuk JSON
     public function getPrograms(Request $request)
    {
    try {
        $query = Program::query();

        // Filter agar tidak menampilkan program dengan status 'nonactive'
        $query->where('status', '!=', 'nonactive');

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
                'message' => 'Failed to fetch programs',
                'error' => $e->getMessage()
            ], 500);
        }
    }

 
     public function getProgramDetail($code)
     {
         try {
             $program = Program::where('code', $code)->firstOrFail();
             
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
}
