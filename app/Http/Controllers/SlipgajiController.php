<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class SlipgajiController extends Controller
{
    public function index()
    {
        return Redirect::to('/dashboard')->with(['warning' => 'Fitur Slip Gaji tidak diaktifkan.']);
    }

    public function cetak($bulan, $tahun)
    {
        return Redirect::to('/dashboard')->with(['warning' => 'Fitur Slip Gaji tidak diaktifkan.']);
    }

    public function cetakslipgaji($bulan, $tahun)
    {
        return Redirect::to('/dashboard')->with(['warning' => 'Fitur Slip Gaji tidak diaktifkan.']);
    }

    public function cetakthr($bulan, $tahun)
    {
        return Redirect::to('/dashboard')->with(['warning' => 'Fitur Slip Gaji tidak diaktifkan.']);
    }
}
