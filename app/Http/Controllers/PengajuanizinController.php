<?php

namespace App\Http\Controllers;

use App\Models\Cabang;
use App\Models\Disposisiizinabsen;
use App\Models\Disposisiizincuti;
use App\Models\Disposisiizinsakit;
use App\Models\Izinabsen;
use App\Models\Izincuti;
use App\Models\Izinsakit;
use App\Models\Izinterlambat;
use App\Models\Izinkeluarkantor;
use App\Models\Izinpulang;
use App\Models\Karyawan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class PengajuanizinController extends Controller
{

    public function buatkode($nomor_terakhir, $kunci, $jumlah_karakter = 0)
    {
        /* mencari nomor baru dengan memecah nomor terakhir dan menambahkan 1
        string nomor baru dibawah ini harus dengan format XXX000000
        untuk penggunaan dalam format lain anda harus menyesuaikan sendiri */
        $nomor_baru = intval(substr($nomor_terakhir, strlen($kunci))) + 1;
        //    menambahkan nol didepan nomor baru sesuai panjang jumlah karakter
        $nomor_baru_plus_nol = str_pad($nomor_baru, $jumlah_karakter, "0", STR_PAD_LEFT);
        //    menyusun kunci dan nomor baru
        $kode = $kunci . $nomor_baru_plus_nol;
        return $kode;
    }

    public function createizinterlambat()
    {
        return view('pengajuanizin.createizinterlambat');
    }

    public function createizinabsen()
    {
        return view('pengajuanizin.createizinabsen');
    }

    public function createizinkeluar()
    {
        return view('pengajuanizin.createizinkeluar');
    }

    public function createizinpulang()
    {
        return view('pengajuanizin.createizinpulang');
    }

    public function createsakit()
    {
        return view('pengajuanizin.createsakit');
    }

    public function createcuti()
    {
        $nik = Auth::guard('karyawan')->user()->nik;
        $tahun_ini = date("Y");
        
        $hak_cuti = DB::table('hrd_hak_cuti')
            ->where('nik', $nik)
            ->where('kode_cuti', 'C01')
            ->where('tahun', $tahun_ini)
            ->first();
        
        if ($hak_cuti) {
            $jatah_cuti = $hak_cuti->jml_hari;
        } else {
            $cuti_tahunan = DB::table('hrd_jeniscuti')->where('kode_cuti', 'C01')->first();
            $jatah_cuti = $cuti_tahunan != null ? $cuti_tahunan->jml_hari : 12;
        }

        $cuti_terpakai = DB::table('pengajuan_izin')
            ->where('nik', $nik)
            ->where('status', 'c')
            ->where('status_approved', 1)
            ->where('jenis_cuti', 'C01')
            ->whereYear('tgl_izin', $tahun_ini)
            ->sum('jmlhari');

        $sisa_cuti = max(0, $jatah_cuti - $cuti_terpakai);

        $mastercuti = DB::table('hrd_jeniscuti')->get();
        $mastercutikhusus = DB::table('hrd_jeniscuti_khusus')->get();
        return view('pengajuanizin.createcuti', compact('mastercuti', 'mastercutikhusus', 'sisa_cuti', 'jatah_cuti', 'cuti_terpakai'));
    }


    public function store(Request $request)
    {
        $nik = Auth::guard('karyawan')->user()->nik;
        $karyawan = Karyawan::where('nik', $nik)->first();
        $request->validate([
            'dari' => 'required',
            'keterangan' => 'required',
        ]);
        DB::beginTransaction();
        try {
            $jenis_izin = $request->jenis_izin ?? 'TM';
            $k = new Karyawan();
            $karyawan = $k->getKaryawan($nik);

            if ($jenis_izin == 'TL') {
                $lastizin = DB::table('hrd_izinterlambat')->select('kode_izin_terlambat')
                    ->whereRaw('YEAR(tanggal)="' . date('Y', strtotime($request->dari)) . '"')
                    ->whereRaw('MONTH(tanggal)="' . date('m', strtotime($request->dari)) . '"')
                    ->orderBy("kode_izin_terlambat", "desc")
                    ->first();
                $last_kode_izin = $lastizin != null ? $lastizin->kode_izin_terlambat : '';
                $kode_izin  = buatkode($last_kode_izin, "IT"  . date('ym', strtotime($request->dari)), 4);

                DB::table('hrd_izinterlambat')->insert([
                    'kode_izin_terlambat' => $kode_izin,
                    'nik' => $nik,
                    'kode_jabatan' => $karyawan->kode_jabatan,
                    'kode_dept' => $karyawan->kode_dept,
                    'kode_cabang' => $karyawan->kode_cabang,
                    'tanggal' => $request->dari,
                    'jam_terlambat' => $request->jam_terlambat,
                    'keterangan' => $request->keterangan,
                    'status' => 0,
                    'direktur' => 0,
                    'id_user' => 1,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
                
                DB::table('pengajuan_izin')->insert([
                    'kode_izin' => $kode_izin,
                    'nik' => $nik,
                    'tgl_izin' => $request->dari,
                    'dari' => $request->dari,
                    'sampai' => $request->dari,
                    'status' => 'i',
                    'status_approved' => 0,
                    'keterangan' => $request->keterangan,
                    'jmlhari' => 1,
                    'jenis_izin' => 'TL',
                    'jam_keluar' => $request->jam_terlambat,
                    'created_by' => 'user'
                ]);
            } elseif ($jenis_izin == 'KL') {
                $lastizin = DB::table('hrd_izinkeluar')->select('kode_izin_keluar')
                    ->whereRaw('YEAR(tanggal)="' . date('Y', strtotime($request->dari)) . '"')
                    ->whereRaw('MONTH(tanggal)="' . date('m', strtotime($request->dari)) . '"')
                    ->orderBy("kode_izin_keluar", "desc")
                    ->first();
                $last_kode_izin = $lastizin != null ? $lastizin->kode_izin_keluar : '';
                $kode_izin  = buatkode($last_kode_izin, "IK"  . date('ym', strtotime($request->dari)), 4);

                DB::table('hrd_izinkeluar')->insert([
                    'kode_izin_keluar' => $kode_izin,
                    'nik' => $nik,
                    'kode_jabatan' => $karyawan->kode_jabatan,
                    'kode_dept' => $karyawan->kode_dept,
                    'kode_cabang' => $karyawan->kode_cabang,
                    'tanggal' => $request->dari,
                    'jam_keluar' => $request->jam_keluar,
                    'jam_kembali' => '00:00:00',
                    'keterangan' => $request->keterangan,
                    'status' => 0,
                    'direktur' => 0,
                    'id_user' => 1,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
                
                DB::table('pengajuan_izin')->insert([
                    'kode_izin' => $kode_izin,
                    'nik' => $nik,
                    'tgl_izin' => $request->dari,
                    'dari' => $request->dari,
                    'sampai' => $request->dari,
                    'status' => 'i',
                    'status_approved' => 0,
                    'keterangan' => $request->keterangan,
                    'jmlhari' => 1,
                    'jenis_izin' => 'KL',
                    'jam_keluar' => $request->jam_keluar,
                    'created_by' => 'user'
                ]);
            } elseif ($jenis_izin == 'PL') {
                $lastizin = DB::table('hrd_izinpulang')->select('kode_izin_pulang')
                    ->whereRaw('YEAR(tanggal)="' . date('Y', strtotime($request->dari)) . '"')
                    ->whereRaw('MONTH(tanggal)="' . date('m', strtotime($request->dari)) . '"')
                    ->orderBy("kode_izin_pulang", "desc")
                    ->first();
                $last_kode_izin = $lastizin != null ? $lastizin->kode_izin_pulang : '';
                $kode_izin  = buatkode($last_kode_izin, "IP"  . date('ym', strtotime($request->dari)), 4);

                DB::table('hrd_izinpulang')->insert([
                    'kode_izin_pulang' => $kode_izin,
                    'nik' => $nik,
                    'kode_jabatan' => $karyawan->kode_jabatan,
                    'kode_dept' => $karyawan->kode_dept,
                    'kode_cabang' => $karyawan->kode_cabang,
                    'tanggal' => $request->dari,
                    'jam_pulang' => $request->jam_pulang,
                    'keterangan' => $request->keterangan,
                    'status_approved' => 0,
                    'direktur' => 0,
                    'id_user' => 1,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
                
                DB::table('pengajuan_izin')->insert([
                    'kode_izin' => $kode_izin,
                    'nik' => $nik,
                    'tgl_izin' => $request->dari,
                    'dari' => $request->dari,
                    'sampai' => $request->dari,
                    'status' => 'i',
                    'status_approved' => 0,
                    'keterangan' => $request->keterangan,
                    'jmlhari' => 1,
                    'jenis_izin' => 'PL',
                    'jam_pulang' => $request->jam_pulang,
                    'created_by' => 'user'
                ]);
            } else {
                $sampai = $request->sampai ?? $request->dari;
                $jmlhari = hitungHari($request->dari, $sampai);
                if ($jmlhari > 3) {
                    return Redirect::back()->with(messageError('Tidak Boleh Lebih dari 3 Hari!'));
                }

                $lastizin = Izinabsen::select('kode_izin')
                    ->whereRaw('YEAR(dari)="' . date('Y', strtotime($request->dari)) . '"')
                    ->whereRaw('MONTH(dari)="' . date('m', strtotime($request->dari)) . '"')
                    ->orderBy("kode_izin", "desc")
                    ->first();
                $last_kode_izin = $lastizin != null ? $lastizin->kode_izin : '';
                $kode_izin  = buatkode($last_kode_izin, "IA"  . date('ym', strtotime($request->dari)), 4);

                Izinabsen::create([
                    'kode_izin' => $kode_izin,
                    'nik' => $nik,
                    'kode_jabatan' => $karyawan->kode_jabatan,
                    'kode_dept' => $karyawan->kode_dept,
                    'kode_cabang' => $karyawan->kode_cabang,
                    'tanggal' => $request->dari,
                    'dari' => $request->dari,
                    'sampai' => $sampai,
                    'keterangan' => $request->keterangan,
                    'status' => 0,
                    'direktur' => 0,
                    'id_user' => 1,
                ]);

                DB::table('pengajuan_izin')->insert([
                    'kode_izin' => $kode_izin,
                    'nik' => $nik,
                    'tgl_izin' => $request->dari,
                    'dari' => $request->dari,
                    'sampai' => $sampai,
                    'status' => 'i',
                    'status_approved' => 0,
                    'keterangan' => $request->keterangan,
                    'jmlhari' => $jmlhari,
                    'jenis_izin' => 'TM',
                    'created_by' => 'user'
                ]);

                $roles_approve = cekRoleapprovepresensi($karyawan->kode_dept, $karyawan->kode_cabang, $karyawan->kategori, $karyawan->kode_jabatan);
                $index_role = 0;
                $tanggal_hariini = date('Y-m-d');
                $lastdisposisi = Disposisiizinabsen::whereRaw('date(created_at)="' . $tanggal_hariini . '"')
                    ->orderBy('kode_disposisi', 'desc')
                    ->first();
                $last_kodedisposisi = $lastdisposisi != null ? $lastdisposisi->kode_disposisi : '';
                $format = "DPIA" . date('Ymd');
                $kode_disposisi = buatkode($last_kodedisposisi, $format, 4);
                try {
                    $cek_user_approve = User::join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
                        ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
                        ->where('roles.name', $roles_approve[$index_role])
                        ->where('users.status', '1')
                        ->first();
                } catch (\Exception $e) {
                    $cek_user_approve = null;
                }

                if ($cek_user_approve == null) {
                    $cek_user_approve = User::first();
                }

                Disposisiizinabsen::create([
                    'kode_disposisi' => $kode_disposisi,
                    'kode_izin' => $kode_izin,
                    'id_pengirim' => 1,
                    'id_penerima' => $cek_user_approve->id,
                    'status' => 0
                ]);
            }
            DB::commit();
            return Redirect::back()->with(messageSuccess('Data Berhasil Disimpan'));
        } catch (\Exception $e) {
            DB::rollBack();
            return Redirect::back()->with(messageError($e->getMessage()));
        }
    }



    public function storesakit(Request $request)
    {
        $nik = Auth::guard('karyawan')->user()->nik;
        $karyawan = Karyawan::where('nik', $nik)->first();
        $request->validate([
            'dari' => 'required',
            'sampai' => 'required',
            'keterangan' => 'required',
        ]);
        DB::beginTransaction();
        try {
            // $jmlhari = hitungHari($request->dari, $request->sampai);
            // if ($jmlhari > 3) {
            //     return Redirect::back()->with(messageError('Tidak Boleh Lebih dari 3 Hari!'));
            // }

            $lastizinsakit = Izinsakit::select('kode_izin_sakit')
                ->whereRaw('YEAR(tanggal)="' . date('Y', strtotime($request->dari)) . '"')
                ->whereRaw('MONTH(tanggal)="' . date('m', strtotime($request->dari)) . '"')
                ->orderBy("kode_izin_sakit", "desc")
                ->first();
            $last_kode_izin_sakit = $lastizinsakit != null ? $lastizinsakit->kode_izin_sakit : '';
            $kode_izin_sakit  = buatkode($last_kode_izin_sakit, "IS"  . date('ym', strtotime($request->dari)), 4);


            $k = new Karyawan();
            $karyawan = $k->getKaryawan($nik);

            $data_sid = [];
            if ($request->hasfile('sid')) {
                $sid_name =  $kode_izin_sakit . "." . $request->file('sid')->getClientOriginalExtension();
                $destination_sid_path = "/public/uploads/sid";
                $sid = $sid_name;
                $data_sid = [
                    'doc_sid' => $sid,
                ];
            }

            $dataizinsakit = [
                'kode_izin_sakit' => $kode_izin_sakit,
                'nik' => $nik,
                'kode_jabatan' => $karyawan->kode_jabatan,
                'kode_dept' => $karyawan->kode_dept,
                'kode_cabang' => $karyawan->kode_cabang,
                'tanggal' => $request->dari,
                'dari' => $request->dari,
                'sampai' => $request->sampai,
                'keterangan' => $request->keterangan,
                'status' => 0,
                'direktur' => 0,
                'id_user' => 1,
            ];
            $data = array_merge($dataizinsakit, $data_sid);
            $simpandatasakit = Izinsakit::create($data);
            if ($simpandatasakit) {
                if ($request->hasfile('sid')) {
                    $request->file('sid')->storeAs($destination_sid_path, $sid_name);
                }
                
                $jmlhari = hitungHari($request->dari, $request->sampai);
                DB::table('pengajuan_izin')->insert([
                    'kode_izin' => $kode_izin_sakit,
                    'nik' => $nik,
                    'tgl_izin' => $request->dari,
                    'dari' => $request->dari,
                    'sampai' => $request->sampai,
                    'status' => 's',
                    'status_approved' => 0,
                    'keterangan' => $request->keterangan,
                    'jmlhari' => $jmlhari,
                    'sid' => isset($sid_name) ? $sid_name : null,
                    'created_by' => 'user'
                ]);
            }


            // $cekregional = Cabang::where('kode_cabang', $karyawan->kode_cabang)->first();

            $roles_approve = cekRoleapprovepresensi($karyawan->kode_dept, $karyawan->kode_cabang, $karyawan->kategori, $karyawan->kode_jabatan);

            //dd($roles_approve);
            // dd($karyawan->kategori);
            // dd($roles_approve);
            $index_role = 0;
            // Jika Tidak Ada di dalam array




            $tanggal_hariini = date('Y-m-d');
            $lastdisposisi = Disposisiizinsakit::whereRaw('date(created_at)="' . $tanggal_hariini . '"')
                ->orderBy('kode_disposisi', 'desc')
                ->first();
            $last_kodedisposisi = $lastdisposisi != null ? $lastdisposisi->kode_disposisi : '';
            $format = "DPIS" . date('Ymd');
            $kode_disposisi = buatkode($last_kodedisposisi, $format, 4);
            try {
                $cek_user_approve = User::join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
                    ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
                    ->where('roles.name', $roles_approve[$index_role])
                    ->where('users.status', '1')
                    ->first();
            } catch (\Exception $e) {
                $cek_user_approve = null;
            }

            if ($cek_user_approve == null) {
                $cek_user_approve = User::first();
            }


            Disposisiizinsakit::create([
                'kode_disposisi' => $kode_disposisi,
                'kode_izin_sakit' => $kode_izin_sakit,
                'id_pengirim' => 1,
                'id_penerima' => $cek_user_approve->id,
                'status' => 0
            ]);
            DB::commit();
            return Redirect::back()->with(messageSuccess('Data Berhasil Disimpan'));
        } catch (\Exception $e) {
            DB::rollBack();
            return Redirect::back()->with(messageError($e->getMessage()));
        }
    }


    public function storecuti(Request $request)
    {
        $nik = Auth::guard('karyawan')->user()->nik;
        $karyawan = Karyawan::where('nik', $nik)->first();
        $request->validate([
            'dari' => 'required',
            'sampai' => 'required',
            'keterangan' => 'required',
            'kode_cuti' => 'required',
        ]);
        $format = "IC" . date('ym', strtotime($request->dari));
        DB::beginTransaction();
        try {
            // $jmlhari = hitungHari($request->dari, $request->sampai);
            // if ($jmlhari > 3) {
            //     return Redirect::back()->with(messageError('Tidak Boleh Lebih dari 3 Hari!'));
            // }

            $lastizincuti = Izincuti::select('kode_izin_cuti')
                // ->whereRaw('YEAR(tanggal)="' . date('Y', strtotime($request->dari)) . '"')
                // ->whereRaw('MONTH(tanggal)="' . date('m', strtotime($request->dari)) . '"')
                ->whereRaw('LEFT(kode_izin_cuti,6)="' . $format . '"')
                ->orderBy("kode_izin_cuti", "desc")
                ->first();
            $last_kode_izin_cuti = $lastizincuti != null ? $lastizincuti->kode_izin_cuti : '';
            $kode_izin_cuti  = buatkode($last_kode_izin_cuti, "IC"  . date('ym', strtotime($request->dari)), 4);


            $k = new Karyawan();
            $karyawan = $k->getKaryawan($nik);

            $data_cuti = [];
            if ($request->hasfile('doc_cuti')) {
                $cuti_name =  $kode_izin_cuti . "." . $request->file('doc_cuti')->getClientOriginalExtension();
                $destination_cuti_path = "/public/uploads/cuti";
                $cuti = $cuti_name;
                $data_cuti = [
                    'doc_cuti' => $cuti,
                ];
            }

            $dataizincuti = [
                'kode_izin_cuti' => $kode_izin_cuti,
                'nik' => $nik,
                'kode_jabatan' => $karyawan->kode_jabatan,
                'kode_dept' => $karyawan->kode_dept,
                'kode_cabang' => $karyawan->kode_cabang,
                'tanggal' => $request->dari,
                'dari' => $request->dari,
                'sampai' => $request->sampai,
                'kode_cuti' => $request->kode_cuti,
                'kode_cuti_khusus' => $request->kode_cuti == 'C03' ? $request->kode_cuti_khusus : null,
                'keterangan' => $request->keterangan,
                'status' => 0,
                'direktur' => 0,
                'id_user' => 1,
            ];

            $data = array_merge($dataizincuti, $data_cuti);
            $simpandatacuti = Izincuti::create($data);
            if ($simpandatacuti) {
                if ($request->hasfile('doc_cuti')) {
                    $request->file('doc_cuti')->storeAs($destination_cuti_path, $cuti_name);
                }
                
                $jmlhari = hitungHari($request->dari, $request->sampai);
                DB::table('pengajuan_izin')->insert([
                    'kode_izin' => $kode_izin_cuti,
                    'nik' => $nik,
                    'tgl_izin' => $request->dari,
                    'dari' => $request->dari,
                    'sampai' => $request->sampai,
                    'status' => 'c',
                    'status_approved' => 0,
                    'keterangan' => $request->keterangan,
                    'jmlhari' => $jmlhari,
                    'jenis_cuti' => $request->kode_cuti,
                    'sid' => isset($cuti_name) ? $cuti_name : null,
                    'created_by' => 'user'
                ]);
            }



            // $cekregional = Cabang::where('kode_cabang', $karyawan->kode_cabang)->first();

            $roles_approve = cekRoleapprovepresensi($karyawan->kode_dept, $karyawan->kode_cabang, $karyawan->kategori, $karyawan->kode_jabatan);

            //dd($roles_approve);
            // dd($karyawan->kategori);
            // dd($roles_approve);
            $index_role = 0;
            // Jika Tidak Ada di dalam array




            $tanggal_hariini = date('Y-m-d');
            $lastdisposisi = Disposisiizincuti::whereRaw('date(created_at)="' . $tanggal_hariini . '"')
                ->orderBy('kode_disposisi', 'desc')
                ->first();
            $last_kodedisposisi = $lastdisposisi != null ? $lastdisposisi->kode_disposisi : '';
            $format = "DPIC" . date('Ymd');
            $kode_disposisi = buatkode($last_kodedisposisi, $format, 4);
            try {
                $cek_user_approve = User::join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
                    ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
                    ->where('roles.name', $roles_approve[$index_role])
                    ->where('users.status', '1')
                    ->first();
            } catch (\Exception $e) {
                $cek_user_approve = null;
            }

            if ($cek_user_approve == null) {
                $cek_user_approve = User::first();
            }


            Disposisiizincuti::create([
                'kode_disposisi' => $kode_disposisi,
                'kode_izin_cuti' => $kode_izin_cuti,
                'id_pengirim' => 1,
                'id_penerima' => $cek_user_approve->id,
                'status' => 0
            ]);
            DB::commit();
            return Redirect::back()->with(messageSuccess('Data Berhasil Disimpan'));
        } catch (\Exception $e) {
            DB::rollBack();
            return Redirect::back()->with(messageError($e->getMessage()));
        }
    }
}
