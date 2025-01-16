<?php

namespace App\Http\Controllers;

use App\Models\News;
use App\Models\NewsCategory;
use App\Models\Program;
use App\Models\ProgramCategory;
use App\Models\Testimoni;
use Illuminate\Http\Request;

class LandingController extends Controller
{
    public function index()
    {
        $testimonis = Testimoni::where('publish', '1')->get();
        $programs = Program::where('status', 'active')->get();
        $news = News::where('publish', '1')->orderBy('created_at', 'desc')->get();
        return view('landing', compact('testimonis', 'programs', 'news'));
    }

    public function showProgram($id)
    {
        $program = Program::findOrFail($id);
        return view('landing.pages.detail-program', compact('program'));
    }

    public function showNews($id)
    {
        $news = News::findOrFail($id);
        return view('landing.pages.detail-berita', compact('news'));
    }

    public function allPrograms(Request $request)
    {
        $query = Program::query();
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        if ($request->filled('category')) {
            $query->where('code_category', $request->category);
        }
        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }
        $sortField = $request->input('sort', 'tanggal_berangkat');
        $sortDirection = $request->input('direction', 'asc');
        $query->orderBy($sortField, $sortDirection);
    
        $programs = $query->paginate(12)->appends($request->query());
        $categories = ProgramCategory::all();
        return view('landing.pages.all-program', compact('programs', 'categories'));
    }

    public function allBerita(Request $request){
        $query = News::query()->where('publish', 1);
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        if ($request->filled('category')) {
            $query->where('code_category', $request->category);
        }
        $sortField = $request->input('sort', 'created_at');
        $sortDirection = $request->input('direction', 'desc');
        $query->orderBy($sortField, $sortDirection);

        $news = $query->paginate(12)->appends($request->query());
        $categories = NewsCategory::all();

        return view('landing.pages.all-berita', compact('news', 'categories'));
    }
}
