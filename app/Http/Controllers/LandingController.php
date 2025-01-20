<?php

namespace App\Http\Controllers;

use App\Models\News;
use App\Models\Program;
use App\Models\Testimoni;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Log;

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
    
   public function downloadGuide($file)
    {
        try {
            $path = public_path('documents/' . $file);
            
            if (!file_exists($path)) {
                throw new Exception("File not found: " . $path);
            }
            
            return Response::download($path);
        } catch (Exception $e) {
            Log::error('Error downloading file: ' . $e->getMessage());
            
            return response()->json([
                'error' => 'An error occurred while downloading the file: ' . $e->getMessage()
            ], 500);
        }
    }
}
