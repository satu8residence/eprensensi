<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class HakcutiController extends Controller
{
    public function index(Request $request)
    {
        $nama_lengkap = $request->nama_lengkap;
        $tahun = $request->tahun;

        $query = DB::table('hrd_hak_cuti')
            ->join('karyawan', 'hrd_hak_cuti.nik', '=', 'karyawan.nik')
            ->join('hrd_jeniscuti', 'hrd_hak_cuti.kode_cuti', '=', 'hrd_jeniscuti.kode_cuti')
            ->select('hrd_hak_cuti.*', 'karyawan.nama_lengkap', 'hrd_jeniscuti.nama_cuti');

        if (!empty($nama_lengkap)) {
            $query->where('karyawan.nama_lengkap', 'like', '%' . $nama_lengkap . '%');
        }

        if (!empty($tahun)) {
            $query->where('hrd_hak_cuti.tahun', $tahun);
        }

        $hakcuti = $query->orderBy('hrd_hak_cuti.tahun', 'desc')
            ->orderBy('karyawan.nama_lengkap')
            ->get();

        $karyawan = DB::table('karyawan')->orderBy('nama_lengkap')->get();
        $jeniscuti = DB::table('hrd_jeniscuti')->orderBy('nama_cuti')->get();

        return view('hakcuti.index', compact('hakcuti', 'karyawan', 'jeniscuti'));
    }

    public function store(Request $request)
    {
        $nik = $request->nik;
        $kode_cuti = $request->kode_cuti;
        $tahun = $request->tahun;
        $jml_hari = $request->jml_hari;

        $data = [
            'nik' => $nik,
            'kode_cuti' => $kode_cuti,
            'tahun' => $tahun,
            'jml_hari' => $jml_hari,
            'created_at' => now(),
            'updated_at' => now()
        ];

        // Cek duplicate
        $cek = DB::table('hrd_hak_cuti')
            ->where('nik', $nik)
            ->where('kode_cuti', $kode_cuti)
            ->where('tahun', $tahun)
            ->count();

        if ($cek > 0) {
            return Redirect::back()->with(['warning' => 'Data Hak Cuti Karyawan Tersebut Sudah Diatur untuk Tahun ' . $tahun]);
        }

        $simpan = DB::table('hrd_hak_cuti')->insert($data);
        if ($simpan) {
            return Redirect::back()->with(['success' => 'Data Hak Cuti Berhasil Disimpan']);
        } else {
            return Redirect::back()->with(['warning' => 'Data Hak Cuti Gagal Disimpan']);
        }
    }

    public function edit(Request $request)
    {
        $id = $request->id;
        $hakcuti = DB::table('hrd_hak_cuti')->where('id', $id)->first();
        $karyawan = DB::table('karyawan')->orderBy('nama_lengkap')->get();
        $jeniscuti = DB::table('hrd_jeniscuti')->orderBy('nama_cuti')->get();

        return view('hakcuti.edit', compact('hakcuti', 'karyawan', 'jeniscuti'));
    }

    public function update($id, Request $request)
    {
        $nik = $request->nik;
        $kode_cuti = $request->kode_cuti;
        $tahun = $request->tahun;
        $jml_hari = $request->jml_hari;

        $data = [
            'nik' => $nik,
            'kode_cuti' => $kode_cuti,
            'tahun' => $tahun,
            'jml_hari' => $jml_hari,
            'updated_at' => now()
        ];

        // Cek duplicate
        $cek = DB::table('hrd_hak_cuti')
            ->where('nik', $nik)
            ->where('kode_cuti', $kode_cuti)
            ->where('tahun', $tahun)
            ->where('id', '!=', $id)
            ->count();

        if ($cek > 0) {
            return Redirect::back()->with(['warning' => 'Data Hak Cuti Karyawan Tersebut Sudah Diatur untuk Tahun ' . $tahun]);
        }

        $update = DB::table('hrd_hak_cuti')->where('id', $id)->update($data);
        if ($update !== false) {
            return Redirect::back()->with(['success' => 'Data Hak Cuti Berhasil Di Update']);
        } else {
            return Redirect::back()->with(['warning' => 'Data Hak Cuti Gagal Di Update']);
        }
    }

    public function delete($id)
    {
        $hapus = DB::table('hrd_hak_cuti')->where('id', $id)->delete();
        if ($hapus) {
            return Redirect::back()->with(['success' => 'Data Hak Cuti Berhasil Di Hapus']);
        } else {
            return Redirect::back()->with(['warning' => 'Data Hak Cuti Gagal Di Hapus']);
        }
    }
}
