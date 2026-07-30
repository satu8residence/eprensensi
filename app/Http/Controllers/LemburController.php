<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use App\Models\Lembur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;

class LemburController extends Controller
{
    public function index(Request $request)
    {
        $query = DB::table('hrd_lembur_detail')
            ->join('hrd_lembur', 'hrd_lembur_detail.kode_lembur', '=', 'hrd_lembur.kode_lembur')
            ->join('karyawan', 'hrd_lembur_detail.nik', '=', 'karyawan.nik')
            ->leftJoin('cabang', 'karyawan.kode_cabang', '=', 'cabang.kode_cabang')
            ->leftJoin('departemen', 'karyawan.kode_dept', '=', 'departemen.kode_dept')
            ->leftJoin('presensi', function ($join) {
                $join->on('hrd_lembur_detail.nik', '=', 'presensi.nik')
                    ->on('hrd_lembur.tanggal', '=', 'presensi.tgl_presensi');
            })
            ->select(
                'hrd_lembur_detail.id as detail_id',
                'hrd_lembur_detail.nik',
                'karyawan.nama_lengkap',
                'cabang.nama_cabang',
                'hrd_lembur.kode_lembur',
                'hrd_lembur.tanggal',
                'hrd_lembur.tanggal_dari',
                'hrd_lembur.tanggal_sampai',
                'hrd_lembur.keterangan',
                'hrd_lembur.kategori',
                'hrd_lembur.istirahat',
                'hrd_lembur.status',
                'presensi.jam_in',
                'presensi.jam_out'
            );

        if (!empty($request->dari) && !empty($request->sampai)) {
            $query->whereBetween('hrd_lembur.tanggal', [$request->dari, $request->sampai]);
        }

        if (!empty($request->kode_cabang)) {
            $query->where('karyawan.kode_cabang', $request->kode_cabang);
        }

        if (!empty($request->kode_dept)) {
            $query->where('karyawan.kode_dept', $request->kode_dept);
        }

        if ($request->status !== null && $request->status !== '') {
            $query->where('hrd_lembur.status', $request->status);
        }

        if (!empty($request->nama_karyawan)) {
            $query->where('karyawan.nama_lengkap', 'like', '%' . $request->nama_karyawan . '%');
        }

        $lembur = $query->orderBy('hrd_lembur.tanggal', 'desc')->paginate(10);
        $lembur->appends($request->all());

        $cabang = DB::table('cabang')->orderBy('nama_cabang')->get();
        $departemen = DB::table('departemen')->orderBy('nama_dept')->get();
        $karyawan = DB::table('karyawan')->orderBy('nama_lengkap')->get();

        return view('lembur.index', compact('lembur', 'cabang', 'departemen', 'karyawan'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'dari_jam' => 'required',
            'sampai_jam' => 'required',
            'kategori' => 'required',
            'istirahat' => 'required',
            'keterangan' => 'required',
            'nik' => 'required|array'
        ]);

        $tanggal = $request->tanggal;
        $tanggal_dari = $tanggal . ' ' . $request->dari_jam . ':00';
        
        if ($request->sampai_jam < $request->dari_jam) {
            $tanggal_sampai = date('Y-m-d', strtotime('+1 day', strtotime($tanggal))) . ' ' . $request->sampai_jam . ':00';
        } else {
            $tanggal_sampai = $tanggal . ' ' . $request->sampai_jam . ':00';
        }

        $kode_lembur = 'L-' . date('ymd', strtotime($tanggal)) . '-' . strtoupper(Str::random(4));

        try {
            DB::beginTransaction();

            DB::table('hrd_lembur')->insert([
                'kode_lembur' => $kode_lembur,
                'tanggal' => $tanggal,
                'tanggal_dari' => $tanggal_dari,
                'tanggal_sampai' => $tanggal_sampai,
                'keterangan' => $request->keterangan,
                'kategori' => $request->kategori,
                'istirahat' => $request->istirahat,
                'status' => 0, // Pending
                'created_at' => now(),
                'updated_at' => now()
            ]);

            foreach ($request->nik as $n) {
                DB::table('hrd_lembur_detail')->insert([
                    'kode_lembur' => $kode_lembur,
                    'nik' => $n,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }

            DB::commit();
            return Redirect::back()->with(['success' => 'Data Lembur Berhasil Disimpan']);
        } catch (\Exception $e) {
            DB::rollBack();
            return Redirect::back()->with(['warning' => 'Gagal Menyimpan Data Lembur: ' . $e->getMessage()]);
        }
    }

    public function edit(Request $request)
    {
        $kode_lembur = $request->kode_lembur;
        $lembur = DB::table('hrd_lembur')->where('kode_lembur', $kode_lembur)->first();
        $assigned_niks = DB::table('hrd_lembur_detail')->where('kode_lembur', $kode_lembur)->pluck('nik')->toArray();
        
        // Return JSON response for AJAX modal populate
        return response()->json([
            'lembur' => $lembur,
            'assigned_niks' => $assigned_niks
        ]);
    }

    public function update(Request $request, $kode_lembur)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'dari_jam' => 'required',
            'sampai_jam' => 'required',
            'kategori' => 'required',
            'istirahat' => 'required',
            'keterangan' => 'required',
            'nik' => 'required|array'
        ]);

        $tanggal = $request->tanggal;
        $tanggal_dari = $tanggal . ' ' . $request->dari_jam . ':00';
        
        if ($request->sampai_jam < $request->dari_jam) {
            $tanggal_sampai = date('Y-m-d', strtotime('+1 day', strtotime($tanggal))) . ' ' . $request->sampai_jam . ':00';
        } else {
            $tanggal_sampai = $tanggal . ' ' . $request->sampai_jam . ':00';
        }

        try {
            DB::beginTransaction();

            DB::table('hrd_lembur')->where('kode_lembur', $kode_lembur)->update([
                'tanggal' => $tanggal,
                'tanggal_dari' => $tanggal_dari,
                'tanggal_sampai' => $tanggal_sampai,
                'keterangan' => $request->keterangan,
                'kategori' => $request->kategori,
                'istirahat' => $request->istirahat,
                'updated_at' => now()
            ]);

            // Re-sync detail assigned employees
            DB::table('hrd_lembur_detail')->where('kode_lembur', $kode_lembur)->delete();
            foreach ($request->nik as $n) {
                DB::table('hrd_lembur_detail')->insert([
                    'kode_lembur' => $kode_lembur,
                    'nik' => $n,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }

            DB::commit();
            return Redirect::back()->with(['success' => 'Data Lembur Berhasil Diupdate']);
        } catch (\Exception $e) {
            DB::rollBack();
            return Redirect::back()->with(['warning' => 'Gagal Mengupdate Data Lembur: ' . $e->getMessage()]);
        }
    }

    public function delete($kode_lembur)
    {
        try {
            DB::table('hrd_lembur')->where('kode_lembur', $kode_lembur)->delete();
            return Redirect::back()->with(['success' => 'Data Lembur Berhasil Dihapus']);
        } catch (\Exception $e) {
            return Redirect::back()->with(['warning' => 'Gagal Menghapus Data Lembur: ' . $e->getMessage()]);
        }
    }

    public function approve($kode_lembur)
    {
        $update = DB::table('hrd_lembur')->where('kode_lembur', $kode_lembur)->update(['status' => 1]);
        if ($update) {
            return Redirect::back()->with(['success' => 'Pengajuan Lembur Berhasil Disetujui']);
        } else {
            return Redirect::back()->with(['warning' => 'Gagal Menyetujui Pengajuan Lembur']);
        }
    }

    public function reject($kode_lembur)
    {
        $update = DB::table('hrd_lembur')->where('kode_lembur', $kode_lembur)->update(['status' => 2]);
        if ($update) {
            return Redirect::back()->with(['success' => 'Pengajuan Lembur Berhasil Ditolak']);
        } else {
            return Redirect::back()->with(['warning' => 'Gagal Menolak Pengajuan Lembur']);
        }
    }

    public function getkaryawanbydept(Request $request)
    {
        $kode_dept = $request->kode_dept;
        $karyawan = DB::table('karyawan')->where('kode_dept', $kode_dept)->orderBy('nama_lengkap')->get();
        return response()->json($karyawan);
    }
}
