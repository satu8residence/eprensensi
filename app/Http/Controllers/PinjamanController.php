<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class PinjamanController extends Controller
{
    public function index(Request $request)
    {
        return Redirect::to('/dashboard')->with(['warning' => 'Fitur Pinjaman tidak diaktifkan.']);
    }

    public function show($no_pinjaman)
    {
        return Redirect::to('/dashboard')->with(['warning' => 'Fitur Pinjaman tidak diaktifkan.']);
    }

    public function simulasi()
    {
        return Redirect::to('/dashboard')->with(['warning' => 'Fitur Pinjaman tidak diaktifkan.']);
    }
}
