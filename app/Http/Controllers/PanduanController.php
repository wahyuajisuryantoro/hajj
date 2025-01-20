<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PanduanController extends Controller
{
    public function hapusAkun()
    {
        return view('landing.pages.hapus-akun');
    }
}
