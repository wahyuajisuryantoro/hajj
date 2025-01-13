<?php

namespace App\Http\Controllers;

use App\Models\News;
use App\Models\Program;
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
}
