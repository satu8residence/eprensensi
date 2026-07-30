<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class CutiController extends Controller
{
    public function index(Request $request)
    {
        $nama_cuti = $request->nama_cuti;
        $query = DB::table('hrd_jeniscuti');
        if (!empty($nama_cuti)) {
            $query->where('nama_cuti', 'like', '%' . $nama_cuti . '%');
        }
        $cuti = $query->orderBy('kode_cuti')->get();
        return view('cuti.index', compact('cuti'));
    }

    public function store(Request $request)
    {
        $kode_cuti = $request->kode_cuti;
        $nama_cuti = $request->nama_cuti;
        $jml_hari = $request->jml_hari;
        
        $data = [
            'kode_cuti' => $kode_cuti,
            'nama_cuti' => $nama_cuti,
            'jml_hari' => $jml_hari,
            'created_at' => now(),
            'updated_at' => now()
        ];

        $cek = DB::table('hrd_jeniscuti')->where('kode_cuti', $kode_cuti)->count();
        if ($cek > 0) {
            return Redirect::back()->with(['warning' => 'Data dengan Kode Cuti ' . $kode_cuti . ' Sudah Ada']);
        }
        
        $simpan = DB::table('hrd_jeniscuti')->insert($data);
        if ($simpan) {
            return Redirect::back()->with(['success' => 'Data Berhasil Disimpan']);
        } else {
            return Redirect::back()->with(['warning' => 'Data Gagal Disimpan']);
        }
    }

    public function edit(Request $request)
    {
        $kode_cuti = $request->kode_cuti;
        $cuti = DB::table('hrd_jeniscuti')->where('kode_cuti', $kode_cuti)->first();
        return view('cuti.edit', compact('cuti'));
    }

    public function update($kode_cuti, Request $request)
    {
        $nama_cuti = $request->nama_cuti;
        $jml_hari = $request->jml_hari;
        
        $data = [
            'nama_cuti' => $nama_cuti,
            'jml_hari' => $jml_hari,
            'updated_at' => now()
        ];

        $update = DB::table('hrd_jeniscuti')->where('kode_cuti', $kode_cuti)->update($data);
        if ($update !== false) {
            return Redirect::back()->with(['success' => 'Data Berhasil Di Update']);
        } else {
            return Redirect::back()->with(['warning' => 'Data Gagal Di Update']);
        }
    }

    public function delete($kode_cuti)
    {
        $hapus = DB::table('hrd_jeniscuti')->where('kode_cuti', $kode_cuti)->delete();
        if ($hapus) {
            return Redirect::back()->with(['success' => 'Data Berhasil Di Hapus']);
        } else {
            return Redirect::back()->with(['warning' => 'Data Gagal Di Hapus']);
        }
    }
}
