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
}
