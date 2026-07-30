<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;

class KaryawanController extends Controller
{
    public function index(Request $request)
    {

        $query = Karyawan::query();
        $query->select('karyawan.*', 'nama_dept', 'nama_jadwal');
        $query->join('departemen', 'karyawan.kode_dept', '=', 'departemen.kode_dept');
        $query->leftJoin('jadwal_kerja', 'karyawan.kode_jadwal', '=', 'jadwal_kerja.kode_jadwal');
        $query->orderBy('nama_lengkap');
        if (!empty($request->nama_karyawan)) {
            $query->where('nama_lengkap', 'like', '%' . $request->nama_karyawan . '%');
        }

        if (!empty($request->kode_dept)) {
            $query->where('karyawan.kode_dept', $request->kode_dept);
        }
        $karyawan = $query->paginate(10);

        $days = [
            'Sunday' => 'Minggu',
            'Monday' => 'Senin',
            'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis',
            'Friday' => 'Jumat',
            'Saturday' => 'Sabtu'
        ];
        $hari_indo = $days[date('l')] ?? 'Senin';

        foreach ($karyawan as $k) {
            $cek_by_date = DB::table('konfigurasi_jk_karyawan_by_date')
                ->join('jam_kerja', 'konfigurasi_jk_karyawan_by_date.kode_jam_kerja', '=', 'jam_kerja.kode_jam_kerja')
                ->where('nik', $k->nik)
                ->where('tanggal', date('Y-m-d'))
                ->first();
                
            if ($cek_by_date) {
                $k->nama_jadwal = $cek_by_date->nama_jam_kerja;
            } else {
                $cek_daily = DB::table('konfigurasi_jk_karyawan')
                    ->join('jam_kerja', 'konfigurasi_jk_karyawan.kode_jam_kerja', '=', 'jam_kerja.kode_jam_kerja')
                    ->where('nik', $k->nik)
                    ->where('hari', $hari_indo)
                    ->first();
                    
                if ($cek_daily) {
                    $k->nama_jadwal = $cek_daily->nama_jam_kerja;
                }
            }
        }

        $departemen = DB::table('departemen')->get();
        $cabang = DB::table('cabang')->orderBy('kode_cabang')->get();
        $jadwal_kerja = DB::table('jadwal_kerja')->orderBy('nama_jadwal')->get();
        return view('karyawan.index', compact('karyawan', 'departemen', 'cabang', 'jadwal_kerja'));
    }

    public function store(Request $request)
    {
        $nik = $request->nik;
        $nama_lengkap = $request->nama_lengkap;
        $jabatan = $request->jabatan;
        $no_hp = $request->no_hp;
        $kode_dept = $request->kode_dept;
        $password = !empty($request->password) ? Hash::make($request->password) : Hash::make('12345');
        $kode_cabang = $request->kode_cabang;
        $kode_jadwal = $request->kode_jadwal;
        if ($request->hasFile('foto')) {
            $foto = $nik . "." . $request->file('foto')->getClientOriginalExtension();
        } else {
            $foto = null;
        }

        try {
            $data =  [
                'nik' => $nik,
                'nama_lengkap' => $nama_lengkap,
                'jabatan' => $jabatan,
                'no_hp' => $no_hp,
                'kode_dept' => $kode_dept,
                'foto' => $foto,
                'password' => $password,
                'kode_cabang' => $kode_cabang,
                'kode_jadwal' => $kode_jadwal
            ];
            $simpan = DB::table('karyawan')->insert($data);
            if ($simpan) {
                if ($request->hasFile('foto')) {
                    $folderPath = "public/uploads/karyawan/";
                    $request->file('foto')->storeAs($folderPath, $foto);
                }
                return Redirect::back()->with(['success' => 'Data Berhasil Disimpan']);
            }
        } catch (\Exception $e) {
            if ($e->getCode() == 23000) {
                $message = "Data dengan Nik " . $nik . " Sudah Ada";
            }
            return Redirect::back()->with(['warning' => 'Data Gagal Disimpan' . $message]);
        }
    }

    public function edit(Request $request)
    {
        $nik = $request->nik;
        $departemen = DB::table('departemen')->get();
        $cabang = DB::table('cabang')->orderBy('kode_cabang')->get();
        $jadwal_kerja = DB::table('jadwal_kerja')->orderBy('nama_jadwal')->get();
        $karyawan = DB::table('karyawan')->where('nik', $nik)->first();
        return view('karyawan.edit', compact('departemen', 'karyawan', 'cabang', 'jadwal_kerja'));
    }

    public function update($nik, Request $request)
    {
        $nik = $request->nik;
        $nama_lengkap = $request->nama_lengkap;
        $jabatan = $request->jabatan;
        $no_hp = $request->no_hp;
        $kode_dept = $request->kode_dept;
        $kode_cabang = $request->kode_cabang;
        $kode_jadwal = $request->kode_jadwal;
        $old_foto = $request->old_foto;
        if ($request->hasFile('foto')) {
            $foto = $nik . "." . $request->file('foto')->getClientOriginalExtension();
        } else {
            $foto = $old_foto;
        }

        try {
            $data =  [
                'nama_lengkap' => $nama_lengkap,
                'jabatan' => $jabatan,
                'no_hp' => $no_hp,
                'kode_dept' => $kode_dept,
                'foto' => $foto,
                'kode_cabang' => $kode_cabang,
                'kode_jadwal' => $kode_jadwal
            ];
            
            if (!empty($request->password)) {
                $data['password'] = Hash::make($request->password);
            }
            
            $update = DB::table('karyawan')->where('nik', $nik)->update($data);
            if ($update) {
                if ($request->hasFile('foto')) {
                    $folderPath = "public/uploads/karyawan/";
                    $folderPathOld = "public/uploads/karyawan/" . $old_foto;
                    Storage::delete($folderPathOld);
                    $request->file('foto')->storeAs($folderPath, $foto);
                }
                return Redirect::back()->with(['success' => 'Data Berhasil Update']);
            }
        } catch (\Exception $e) {
            //dd($e->message);
            return Redirect::back()->with(['warning' => 'Data Gagal Diupdate']);
        }
    }

    public function delete($nik)
    {
        $delete = DB::table('karyawan')->where('nik', $nik)->delete();
        if ($delete) {
            return Redirect::back()->with(['success' => 'Data Berhasil Dihapus']);
        } else {
            return Redirect::back()->with(['warning' => 'Data Gagal Dihapus']);
        }
    }

    public function setjamkerja($nik)
    {
        $karyawan = DB::table('karyawan')
            ->select('karyawan.*', 'nama_dept', 'nama_cabang')
            ->leftJoin('cabang', 'karyawan.kode_cabang', '=', 'cabang.kode_cabang')
            ->leftJoin('departemen', 'karyawan.kode_dept', '=', 'departemen.kode_dept')
            ->where('nik', $nik)
            ->first();

        $jamkerja = DB::table('jam_kerja')->orderBy('nama_jam_kerja')->get();
        
        // Fetch current daily configurations mapped by day name
        $daily_schedules = DB::table('konfigurasi_jk_karyawan')
            ->where('nik', $nik)
            ->get()
            ->pluck('kode_jam_kerja', 'hari')
            ->toArray();

        // Fetch custom date configurations
        $date_schedules = DB::table('konfigurasi_jk_karyawan_by_date')
            ->join('jam_kerja', 'konfigurasi_jk_karyawan_by_date.kode_jam_kerja', '=', 'jam_kerja.kode_jam_kerja')
            ->where('nik', $nik)
            ->orderBy('tanggal', 'desc')
            ->get();

        return view('karyawan.setjamkerja', compact('karyawan', 'jamkerja', 'daily_schedules', 'date_schedules'));
    }

    public function storesetjamkerja($nik, Request $request)
    {
        $hari = $request->hari;
        $kode_jam_kerja = $request->kode_jam_kerja;

        try {
            DB::beginTransaction();

            // Clear old daily config first
            DB::table('konfigurasi_jk_karyawan')->where('nik', $nik)->delete();

            foreach ($hari as $key => $h) {
                $shift = $kode_jam_kerja[$key];
                if (!empty($shift)) {
                    DB::table('konfigurasi_jk_karyawan')->insert([
                        'nik' => $nik,
                        'hari' => $h,
                        'kode_jam_kerja' => $shift,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }
            }

            DB::commit();
            return Redirect::back()->with(['success' => 'Jadwal Hari Kerja Berhasil Diupdate']);
        } catch (\Exception $e) {
            DB::rollBack();
            return Redirect::back()->with(['warning' => 'Gagal mengupdate jadwal hari kerja: ' . $e->getMessage()]);
        }
    }

    public function storesetjamkerjabydate($nik, Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'kode_jam_kerja' => 'required'
        ]);

        try {
            DB::table('konfigurasi_jk_karyawan_by_date')->updateOrInsert(
                ['nik' => $nik, 'tanggal' => $request->tanggal],
                [
                    'kode_jam_kerja' => $request->kode_jam_kerja,
                    'created_at' => now(),
                    'updated_at' => now()
                ]
            );

            return Redirect::back()->with(['success' => 'Jadwal Khusus Berhasil Ditambahkan']);
        } catch (\Exception $e) {
            return Redirect::back()->with(['warning' => 'Gagal menambahkan jadwal khusus: ' . $e->getMessage()]);
        }
    }

    public function deletesetjamkerjabydate($id)
    {
        $hapus = DB::table('konfigurasi_jk_karyawan_by_date')->where('id', $id)->delete();
        if ($hapus) {
            return Redirect::back()->with(['success' => 'Jadwal Khusus Berhasil Dihapus']);
        } else {
            return Redirect::back()->with(['warning' => 'Jadwal Khusus Gagal Dihapus']);
        }
    }

    public function resetpassword($nik)
    {
        try {
            DB::table('karyawan')->where('nik', $nik)->update([
                'password' => \Illuminate\Support\Facades\Hash::make('12345')
            ]);
            return Redirect::back()->with(['success' => 'Password Karyawan Berhasil Direset ke 12345']);
        } catch (\Exception $e) {
            return Redirect::back()->with(['warning' => 'Password Karyawan Gagal Direset: ' . $e->getMessage()]);
        }
    }
}
