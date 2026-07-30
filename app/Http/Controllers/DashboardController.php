<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {

        //dd(Auth::guard('karyawan')->user());
        $hariini = date("Y-m-d");
        // $hariini = "2023-05-05";
        $bulanini = date("m") * 1; //1 atau Januari
        $tahunini = date("Y"); // 2023
        $nik = Auth::guard('karyawan')->user()->nik;
        $data['presensi_hariini'] = DB::table('presensi')->where('nik', $nik)->where('tgl_presensi', $hariini)->first();


        //Rekap Presensi
        $data['rekap_presensi'] = DB::table('presensi')
            ->selectRaw('SUM(IF(status="h",1,0)) as jmlhadir,
            SUM(IF(status="i",1,0)) as jmlizin,
            SUM(IF(status="s",1,0)) as jmlsakit,
            SUM(IF( DATE_FORMAT(jam_in,"%H:%i") > DATE_FORMAT(jam_masuk,"%H:%i"),1,0)) as jmlterlambat')
            ->leftjoin('jam_kerja', 'presensi.kode_jam_kerja', '=', 'jam_kerja.kode_jam_kerja')
            ->where('nik', $nik)
            ->whereRaw('MONTH(tgl_presensi)="' . $bulanini . '"')
            ->whereRaw('YEAR(tgl_presensi)="' . $tahunini . '"')
            ->first();

        //Histori 7 Hari Terakhir
        $data['histori'] = DB::table('presensi')
            ->select(
                'presensi.*',
                'presensi.tgl_presensi as tanggal',
                'presensi.location_in as lokasi_in',
                'presensi.location_out as lokasi_out',
                'jam_kerja.jam_masuk as jam_mulai',
                'jam_kerja.jam_pulang as jam_selesai',
                'jam_kerja.lintashari',
                'karyawan.jabatan as kode_jabatan',
                'karyawan.kode_dept',
                DB::raw('NULL as kode_izin_terlambat'),
                DB::raw('NULL as kode_izin_keluar'),
                DB::raw('NULL as jam_keluar'),
                DB::raw('NULL as jam_kembali'),
                'jam_kerja.total_jam',
                'jam_kerja.istirahat',
                'jam_kerja.jam_awal_istirahat',
                'jam_kerja.jam_akhir_istirahat',
                DB::raw('NULL as kode_izin_pulang'),
                'jadwal_kerja.nama_jadwal',
                'jam_kerja.nama_jam_kerja',
                'karyawan.kode_cabang',
                'presensi.status',
                DB::raw('NULL as nama_cuti'),
                DB::raw('NULL as nama_cuti_khusus'),
                DB::raw('NULL as doc_sid')
            )
            ->join('karyawan', 'presensi.nik', '=', 'karyawan.nik')
            ->leftJoin('jam_kerja', 'presensi.kode_jam_kerja', '=', 'jam_kerja.kode_jam_kerja')
            ->leftJoin('jadwal_kerja', 'presensi.kode_jadwal', '=', 'jadwal_kerja.kode_jadwal')
            ->where('presensi.nik', $nik)
            ->where('presensi.tgl_presensi', '<=', $hariini)
            ->orderBy('presensi.tgl_presensi', 'desc')
            ->limit(7)
            ->get();

        $data['histori_lembur'] = DB::table('hrd_lembur_detail')
            ->join('hrd_lembur', 'hrd_lembur_detail.kode_lembur', '=', 'hrd_lembur.kode_lembur')
            ->leftJoin('presensi', function ($join) {
                $join->on('hrd_lembur_detail.nik', '=', 'presensi.nik')
                    ->on('hrd_lembur.tanggal', '=', 'presensi.tgl_presensi');
            })
            ->where('hrd_lembur_detail.nik', $nik)
            ->select(
                'hrd_lembur.tanggal',
                'hrd_lembur.tanggal_dari',
                'hrd_lembur.tanggal_sampai',
                'hrd_lembur.keterangan',
                'hrd_lembur.status',
                'presensi.jam_in',
                'presensi.jam_out'
            )
            ->orderBy('hrd_lembur.tanggal', 'desc')
            ->limit(7)
            ->get();

        $data['namabulan'] = ["", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];


        $jabatan = (object)['nama_jabatan' => Auth::guard('karyawan')->user()->jabatan];
        $kode_dept = Auth::guard('karyawan')->user()->kode_dept;
        $kode_cabang = Auth::guard('karyawan')->user()->kode_cabang;
        $data['jabatan'] = $jabatan;
        $data['kode_dept'] = $kode_dept;
        $data['kode_cabang'] = $kode_cabang;
        $data['bulanini'] = $bulanini;
        $data['tahunini'] = $tahunini;
        if ($kode_dept == "MKT" || $kode_cabang != "PST") {
            return view('dashboard.dashboardwithcamera', $data);
        } else {
            return view('dashboard.dashboard', $data);
        }
    }

    public function dashboardadmin()
    {
        $hariini = date("Y-m-d");
        $rekappresensi = DB::table('presensi')
            ->selectRaw('COUNT(nik) as jmlhadir, IFNULL(SUM(IF(jam_in > "07:00",1,0)),0) as jmlterlambat')
            ->where('tgl_presensi', $hariini)
            ->first();

        $rekapizin = DB::table('pengajuan_izin')
            ->selectRaw('IFNULL(SUM(IF(status="i",1,0)),0) as jmlizin, IFNULL(SUM(IF(status="s",1,0)),0) as jmlsakit')
            ->whereRaw('? BETWEEN dari AND sampai', [$hariini])
            ->where('status_approved', 1)
            ->first();


        return view('dashboard.dashboardadmin', compact('rekappresensi', 'rekapizin'));
    }
}
