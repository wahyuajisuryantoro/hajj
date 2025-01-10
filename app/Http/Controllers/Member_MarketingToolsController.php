<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MarketingTool;

class Member_MarketingToolsController extends Controller
{
    public function eFlayer()
    {
        $title = 'E-Flayer';
        
        $eflayers = MarketingTool::published()
            ->eflayer()
            ->latest()
            ->paginate(6);
            
        return view('pages.marketing-tools.e-flayer', compact('title', 'eflayers'));
    }

    public function videoPromosi()
    {
        $title = 'Video Promosi';
        return view('pages.marketing-tools.video-promosi', compact('title'));
    }
}
