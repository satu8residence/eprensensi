<?php

namespace App\Http\Controllers;

use App\Models\Izinabsen;
use App\Models\Izincuti;
use App\Models\Izinkeluarkantor;
use App\Models\Izinpulang;
use App\Models\Izinsakit;
use App\Models\Izinterlambat;
use App\Models\Karyawan;
use App\Models\Pengajuanizin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;

class PresensiController extends Controller
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

    public function hari_ini()
    {
        $hari = date("D");

        switch ($hari) {
            case 'Sun':
                $hari_ini = "Minggu";
                break;

            case 'Mon':
                $hari_ini = "Senin";
                break;

            case 'Tue':
                $hari_ini = "Selasa";
                break;

            case 'Wed':
                $hari_ini = "Rabu";
                break;

            case 'Thu':
                $hari_ini = "Kamis";
                break;

            case 'Fri':
                $hari_ini = "Jumat";
                break;

            case 'Sat':
                $hari_ini = "Sabtu";
                break;

            default:
                $hari_ini = "Tidak di ketahui";
                break;
        }

        return $hari_ini;
    }


    public function hari_tanggal($tgl)
    {
        $hari = date("D", strtotime($tgl));

        switch ($hari) {
            case 'Sun':
                $hari_ini = "Minggu";
                break;

            case 'Mon':
                $hari_ini = "Senin";
                break;

            case 'Tue':
                $hari_ini = "Selasa";
                break;

            case 'Wed':
                $hari_ini = "Rabu";
                break;

            case 'Thu':
                $hari_ini = "Kamis";
                break;

            case 'Fri':
                $hari_ini = "Jumat";
                break;

            case 'Sat':
                $hari_ini = "Sabtu";
                break;

            default:
                $hari_ini = "Tidak di ketahui";
                break;
        }

        return $hari_ini;
    }


    public function create()
    {
        $hariini = date("Y-m-d");
        $nik = Auth::guard('karyawan')->user()->nik;

        //Cek Apakah Sedang Perjalanan Dinas Ke Cabang lain
        $cekperjalanandinas = null;


        if ($cekperjalanandinas != null) {
            $kode_cabang = $cekperjalanandinas->kode_cabang;
        } else {
            $kode_cabang = Auth::guard('karyawan')->user()->kode_cabang;
        }

        //Cek Lokasi Cabang
        $lok_kantor = DB::table('cabang')->where('kode_cabang', $kode_cabang)->first();
        if ($lok_kantor == null) {
            $pusat = DB::table('konfigurasi_lokasi')->where('id', 1)->first();
            $lok_kantor = (object)[
                'kode_cabang' => 'PST',
                'nama_cabang' => 'Kantor Pusat',
                'lokasi_cabang' => $pusat ? $pusat->lokasi_kantor : '-6.306488,106.666570',
                'radius_cabang' => $pusat ? $pusat->radius : 100
            ];
        }

        // Cek Apakah Sudah Absen
        $cek = DB::table('presensi')->where('tgl_presensi', $hariini)->where('nik', $nik)->count();

        //Cek Apakah Memiliki Jadwal Shift
        $cekjadwalshift = DB::table('konfigurasi_jadwalkerja_detail')
            ->join('konfigurasi_jadwalkerja', 'konfigurasi_jadwalkerja_detail.kode_setjadwal', '=', 'konfigurasi_jadwalkerja.kode_setjadwal')
            ->whereRaw('"' . $hariini . '" >= dari')
            ->whereRaw('"' . $hariini . '" <= sampai')
            ->where('nik', $nik)
            ->first();

        //Cek Apakah Ada Pergantian Shift
        $cekgantishift = null;

        //Jika Ada Pergantian Shift
        if ($cekgantishift != null) {
            $kode_jadwal = $cekgantishift->kode_jadwal;
            //Jika Memiliki Jadwal Shift
        } else if ($cekjadwalshift != null) {
            $kode_jadwal = $cekjadwalshift->kode_jadwal;

            //Jika Sedang Perjalanan Dinas
        } else if ($cekperjalanandinas != null) {
            //Sesuaikan dengan Jadwal Cabang Tujuan
            $cekjadwaldinas = DB::table('jadwal_kerja')
                ->where('nama_jadwal', 'NON SHIFT')
                ->where('kode_cabang', $cekperjalanandinas->kode_cabang)->first();
            $kode_jadwal = $cekjadwaldinas->kode_jadwal;
        } else {

            //Gunakan Jadwal Default atau Fallback ke JD01 (Jadwal Standar)
            $kode_jadwal = Auth::guard('karyawan')->user()->kode_jadwal ?? 'JD01';
        }

        //Tanggal 5 Jam Ketika Besok Libur
        $libur = null;
        $datalibur = null;
        $tanggal_libur = '';

        //Cek Libur Hari ini
        $cekliburhariini = null;

        // Cek Wfh Hari Ini
        $cekwfhhariini = null;

        //Cek Libur Pengganti Hari Minggu
        $cekliburpenggantiminggu = null;

        //Cek Hari Minggu Masuk
        $cekminggumasuk = null;

        $hariini_nama = $this->hari_ini();
        $jadwal = $this->getJamKerjaKaryawan($nik, $hariini, $hariini_nama);

        // Jika tidak ada jadwal hari ini, cek apakah ada presensi lintas hari kemarin
        // yang belum absen pulang. Jika ada, izinkan karyawan absen pulang.
        if ($jadwal == null || empty($jadwal->kode_jam_kerja)) {
            $lastday_create = date('Y-m-d', strtotime('-1 day', strtotime($hariini)));
            $ceklastpresensi_create = DB::table('presensi')
                ->join('jam_kerja', 'presensi.kode_jam_kerja', '=', 'jam_kerja.kode_jam_kerja')
                ->where('presensi.nik', $nik)
                ->where('presensi.tgl_presensi', $lastday_create)
                ->whereNotNull('presensi.jam_in')
                ->whereNull('presensi.jam_out')
                ->where('jam_kerja.lintashari', '1')
                ->first();

            if ($ceklastpresensi_create != null) {
                // Ada shift lintas hari kemarin yang belum pulang, gunakan shift kemarin
                $jadwal = (object) [
                    'kode_jadwal'    => $ceklastpresensi_create->kode_jadwal,
                    'kode_jam_kerja' => $ceklastpresensi_create->kode_jam_kerja,
                    'hari'           => $hariini_nama,
                    'nama_jadwal'    => 'Lintas Hari',
                ];
            }
        }

        //Jika Belum Memiliki Jadwal
        if (($jadwal == null || empty($jadwal->kode_jam_kerja)) && empty($cekminggumasuk)) {
            return view('presensi.notifjadwal');
        }

        $jam_kerja = DB::table('jam_kerja')->where('kode_jam_kerja', $jadwal->kode_jam_kerja)->first();

        $kode_dept =  Auth::guard('karyawan')->user()->kode_dept;
        $kode_cabang =  Auth::guard('karyawan')->user()->kode_cabang;

        if ($kode_dept == "MKT" || $kode_cabang != "PST" || $kode_dept == "ADT") {
            return view('presensi.create_with_camera', compact('cek', 'lok_kantor', 'jam_kerja', 'jadwal'));
        } else {
            return view('presensi.create', compact('cek', 'lok_kantor', 'jam_kerja', 'jadwal'));
        }
    }

    public function store(Request $request)
    {

        $nik = Auth::guard('karyawan')->user()->nik;
        $lock_location = Auth::guard('karyawan')->user()->lock_location;
        $tgl_presensi = date("Y-m-d");

        $cekperjalanandinas = null;
        if ($cekperjalanandinas != null) {
            $kode_cabang = $cekperjalanandinas->kode_cabang;
            $lock_location = 0;
        } else {
            $kode_cabang = Auth::guard('karyawan')->user()->kode_cabang;
        }

        $lastday = date('Y-m-d', strtotime('-1 day', strtotime($tgl_presensi)));
        $jam = date("Y-m-d H:i:s");

        $lok_kantor = DB::table('cabang')->where('kode_cabang', $kode_cabang)->first();
        if ($lok_kantor == null) {
            $pusat = DB::table('konfigurasi_lokasi')->where('id', 1)->first();
            $lok_kantor = (object)[
                'kode_cabang' => 'PST',
                'nama_cabang' => 'Kantor Pusat',
                'lokasi_cabang' => $pusat ? $pusat->lokasi_kantor : '-6.306488,106.666570',
                'radius_cabang' => $pusat ? $pusat->radius : 100
            ];
        }
        $lok = explode(",", $lok_kantor->lokasi_cabang);
        $latitudekantor = $lok[0];
        $longitudekantor = $lok[1];
        $lokasi = $request->lokasi;
        $lokasiuser = explode(",", $lokasi);
        $latitudeuser = $lokasiuser[0];
        $longitudeuser = $lokasiuser[1];
        $statuspresensi = $request->statuspresensi;

        if ($statuspresensi == "masuk") {
            $ket = "in";
        } else {
            $ket = "out";
        }

        if (isset($request->image)) {
            $image = $request->image;
            $folderPath = "public/uploads/absensi/";
            $formatName = $nik . "-" . $tgl_presensi . "-" . $ket;
            $image_parts = explode(";base64", $image);
            $image_base64 = base64_decode($image_parts[1]);
            $fileName = $formatName . ".png";
            $file = $folderPath . $fileName;
        } else {
            $fileName = null;
        }

        //Cek Apakah Memiliki Jadwal Shift Mingguan
        $cekjadwalshift = DB::table('konfigurasi_jadwalkerja_detail')
            ->join('konfigurasi_jadwalkerja', 'konfigurasi_jadwalkerja_detail.kode_setjadwal', '=', 'konfigurasi_jadwalkerja.kode_setjadwal')
            ->whereRaw('"' . $tgl_presensi . '" >= dari')
            ->whereRaw('"' . $tgl_presensi . '" <= sampai')
            ->where('nik', $nik)
            ->first();

        $cekgantishift = null;

        if ($cekgantishift != null) {
            $kode_jadwal = $cekgantishift->kode_jadwal;
        } else if ($cekjadwalshift != null) {
            $kode_jadwal = $cekjadwalshift->kode_jadwal;
        } else if ($cekperjalanandinas != null) {
            $cekjadwaldinas = DB::table('jadwal_kerja')
                ->where('nama_jadwal', 'NON SHIFT')
                ->where('kode_cabang', $cekperjalanandinas->kode_cabang)->first();
            $kode_jadwal = $cekjadwaldinas->kode_jadwal;
        } else {
            $kode_jadwal = Auth::guard('karyawan')->user()->kode_jadwal ?? 'JD01';
        }

        $libur = null;
        $datalibur = null;
        $tanggal_libur = '';
        $ceklembur = null;

        $hariini = $this->hari_tanggal($tgl_presensi);
        $jadwal = $this->getJamKerjaKaryawan($nik, $tgl_presensi, $hariini);

        // Jika tidak ada jadwal hari ini (misal: libur), cek apakah karyawan punya
        // presensi lintas hari (shift malam) dari kemarin yang belum absen pulang.
        // Jika ada, gunakan shift kemarin agar bisa absen pulang.
        $is_lintashari_pulang = false;
        if ($jadwal == null && $statuspresensi == 'pulang') {
            $ceklastpresensi_lintashari = DB::table('presensi')
                ->join('jam_kerja', 'presensi.kode_jam_kerja', '=', 'jam_kerja.kode_jam_kerja')
                ->where('presensi.nik', $nik)
                ->where('presensi.tgl_presensi', $lastday)
                ->whereNotNull('presensi.jam_in')
                ->whereNull('presensi.jam_out')
                ->where('jam_kerja.lintashari', '1')
                ->first();
            if ($ceklastpresensi_lintashari != null) {
                $is_lintashari_pulang = true;
                $jadwal = (object) [
                    'kode_jadwal'    => $ceklastpresensi_lintashari->kode_jadwal,
                    'kode_jam_kerja' => $ceklastpresensi_lintashari->kode_jam_kerja,
                    'hari'           => $hariini,
                    'nama_jadwal'    => 'Lintas Hari',
                ];
            }
        }

        if ($jadwal == null) {
            $jadwal = DB::table('jadwal_kerja_detail')
                ->join('jadwal_kerja', 'jadwal_kerja_detail.kode_jadwal', '=', 'jadwal_kerja.kode_jadwal')
                ->where('hari', $hariini)
                ->where('jadwal_kerja_detail.kode_jadwal', 'JD01')
                ->first();
        }

        $jam_kerja = DB::table('jam_kerja')->where('kode_jam_kerja', $jadwal->kode_jam_kerja)->first();

        $lintashari = $jam_kerja->lintashari;


        $jarak = $this->distance($latitudekantor, $longitudekantor, $latitudeuser, $longitudeuser);
        $radius = round($jarak["meters"]);

        $cek = DB::table('presensi')->where('tgl_presensi', $tgl_presensi)->where('nik', $nik)->first();



        $jam_sekarang = date("H:i:s");


        //cek Izin Terlambat

        $cekizinterlambat = null;

        $kode_izin = $cekizinterlambat != null  ? $cekizinterlambat->kode_izin_terlambat : NULL;

        $kode_dept = Auth::guard('karyawan')->user()->kode_dept;


        if ($radius > $lok_kantor->radius_cabang && $lock_location == 0) {
            echo "error|Maaf Anda Berada Diluar Radius, Jarak Anda " . $radius . " meter dari Kantor|radius";
        } else {
            if ($statuspresensi == "masuk") {
                $jam_masuk = $tgl_presensi . " " . "10:00";
                $jamabsen = $jam;
                if ($kode_jadwal == "JD004" && $jamabsen <= $jam_masuk  || $kode_jadwal == "JD003" && $jamabsen <= $jam_masuk) {
                    echo "error|Maaf Belum Waktunya Absen Masuk|in";
                } else {
                    if ($cek != null && !empty($cek->jam_in)) {
                        echo "error|Maaf Gagal absen, Anda Sudah Melakukan Presensi Masuk|in";
                    } else if ($cek != null && empty($cek->jam_in)) {
                        $data_masuk = [
                            'jam_in' => $jam,
                            'foto_in' => $fileName,
                            'lokasi_in' => $lokasi
                        ];
                        $update = DB::table('presensi')->where('tgl_presensi', $tgl_presensi)->where('nik', $nik)->update($data_masuk);
                        if ($update) {
                            echo "success|Terimkasih, Selamat Bekerja|in";
                            if (isset($request->image)) {
                                Storage::put($file, $image_base64);
                            }
                            // Storage::put($file, $image_base64);
                        } else {
                            echo "error|Maaf Gagal absen, Hubungi Tim It|in";
                        }
                    } else if ($cek == null) {
                        $data = [
                            'nik' => $nik,
                            'tgl_presensi' => $tgl_presensi,
                            'jam_in' => $jam,
                            'foto_in' => $fileName,
                            'location_in' => $lokasi,
                            'kode_jadwal' => $kode_jadwal,
                            'kode_jam_kerja' => $jadwal->kode_jam_kerja,
                            'status' => 'h',
                        ];

                        $simpan = DB::table('presensi')->insert($data);
                        if ($simpan) {
                            echo "success|Terimkasih, Selamat Bekerja|in";
                            if (isset($request->image)) {
                                Storage::put($file, $image_base64);
                            }
                            // Storage::put($file, $image_base64);
                        } else {
                            echo "error|Maaf Gagal absen, Hubungi Tim It|in";
                        }
                    }
                }
            } else if ($statuspresensi == "pulang") {


                $ceklastpresensi = DB::table('presensi')
                    ->join('jam_kerja', 'presensi.kode_jam_kerja', '=', 'jam_kerja.kode_jam_kerja')
                    ->where('nik', $nik)->where('tgl_presensi', $lastday)->first();

                $last_lintashari = $ceklastpresensi != null  ? $ceklastpresensi->lintashari : "";
                $tgl_pulang_shift_3 = date("H:i", strtotime(($jam)));

                $cekjadwalshiftlast = null;
                $kode_jadwal_last = $cekjadwalshiftlast != null ? $cekjadwalshiftlast->kode_jadwal : $kode_jadwal;
                //dd($cekjadwalshiftlast);
                // /echo $tgl_pulang_shift_3;
                $kode_jam_kerja = $jadwal->kode_jam_kerja;
                if (!empty($last_lintashari)) {
                    if ($jam_sekarang > "00:00" && $jam_sekarang <= "14:00") {
                        $tgl_presensi = $lastday;
                    }

                    if ($hariini != "Sabtu") {
                        $tgl_pulang = date('Y-m-d', strtotime('+1 day', strtotime($tgl_presensi)));
                        $jam_pulang = $tgl_pulang . " " . date("H:i", strtotime($ceklastpresensi->jam_pulang));
                    } else {
                        $tgl_pulang = $tgl_presensi;
                        $jam_pulang = $tgl_pulang . " " . date("H:i", strtotime($jam_kerja->jam_pulang));
                    }
                } else {
                    if ($tgl_pulang_shift_3 <= "14:00" && $kode_jadwal_last == "JD004") {
                        $tgl_presensi = $lastday;
                        $tgl_pulang = date('Y-m-d', strtotime('+1 day', strtotime($tgl_presensi)));
                        if ($hariini != "Sabtu") {
                            $jam_pulang = $tgl_pulang . " 07:00";
                            $kode_jam_kerja = "JK08";
                        } else {
                            $jam_pulang = $tgl_pulang . " 22:00";
                            $kode_jam_kerja = "JK15";
                        }

                        $kode_jadwal = "JD004";

                        //echo "A" . $jam_pulang;
                    } else {
                        if ($kode_jadwal == "JD004") {
                            if ($hariini != "Sabtu") {
                                if ($jam_sekarang > "00:00" && $jam_sekarang <= "14:00") {
                                    $tgl_pulang = $tgl_presensi;
                                } else {
                                    $tgl_pulang = date('Y-m-d', strtotime('+1 day', strtotime($tgl_presensi)));
                                }
                            } else {
                                $tgl_pulang = $tgl_presensi;
                            }
                        } else {
                            $tgl_pulang = $tgl_presensi;
                        }
                        $jam_pulang = $tgl_pulang . " " . date("H:i", strtotime($jam_kerja->jam_pulang));

                        // echo "B" . $jam_pulang;
                    }
                }

                //die;
                // echo $jam_pulang;
                // die;

                $jam_pulang_formatted = date("Y-m-d H:i", strtotime($jam_pulang));
                $jamabsen = $jam;
                if (strtotime($jamabsen) < strtotime($jam_pulang)) {
                    echo "error|Maaf Belum Waktunya Absen Pulang, Absen Pulang di Mulai Pada Pukul " . $jam_pulang_formatted . " |out" . $kode_jadwal;
                } else {

                    $cek = DB::table('presensi')->where('tgl_presensi', $tgl_presensi)->where('nik', $nik)->first();
                    if ($cek == null) {
                        $data = [
                            'nik' => $nik,
                            'tgl_presensi' => $tgl_presensi,
                            'jam_out' => $jam,
                            'foto_out' => $fileName,
                            'location_out' => $lokasi,
                            'kode_jadwal' => $kode_jadwal,
                            'kode_jam_kerja' => $kode_jam_kerja,
                            'status' => 'h',
                        ];

                        $simpan = DB::table('presensi')->insert($data);
                        if ($simpan) {
                            echo "success|Terimkasih, Hati Hati Di Jalan|out";
                            if (isset($request->image)) {
                                Storage::put($file, $image_base64);
                            }
                            // Storage::put($file, $image_base64);
                        } else {
                            echo "error|Maaf Gagal absen, Hubungi Tim It|out";
                        }
                    } else if ($cek != null && !empty($cek->jam_out)) {
                        echo "error|Maaf Gagal absen, Anda Sudah Melakukan Presensi Pulang|in";
                    } else if ($cek != null && empty($cek->jam_out)) {
                        $data_masuk = [
                            'jam_out' => $jam,
                            'foto_out' => $fileName,
                            'location_out' => $lokasi
                        ];
                        $update = DB::table('presensi')->where('tgl_presensi', $tgl_presensi)->where('nik', $nik)->update($data_masuk);
                        if ($update) {
                            if (isset($request->image)) {
                                Storage::put($file, $image_base64);
                            }
                            echo "success|Terimkasih, Hati Hati Di Jalan|out";
                            // Storage::put($file, $image_base64);
                        } else {
                            echo "error|Maaf Gagal absen, Hubungi Tim It|out";
                        }
                    }
                }
            }
        }
    }




    //Menghitung Jarak
    function distance($lat1, $lon1, $lat2, $lon2)
    {
        $theta = $lon1 - $lon2;
        $miles = (sin(deg2rad($lat1)) * sin(deg2rad($lat2))) + (cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta)));
        $miles = acos($miles);
        $miles = rad2deg($miles);
        $miles = $miles * 60 * 1.1515;
        $feet = $miles * 5280;
        $yards = $feet / 3;
        $kilometers = $miles * 1.609344;
        $meters = $kilometers * 1000;
        return compact('meters');
    }

    public function editprofile()
    {
        $nik = Auth::guard('karyawan')->user()->nik;
        $karyawan = DB::table('karyawan')->select('karyawan.*', 'nama_lengkap as nama_karyawan')->where('nik', $nik)->first();
        return view('presensi.editprofile', compact('karyawan'));
    }

    public function updateprofile(Request $request)
    {
        $nik = Auth::guard('karyawan')->user()->nik;
        $nama_lengkap = $request->nama_lengkap;
        $no_hp = $request->no_hp;
        $this->validate($request, [
            // check validtion for image or file
            'foto' => 'image|mimes:jpg,png,jpeg,gif,svg|max:1024',
        ]);
        $password = Hash::make($request->password);
        $karyawan = DB::table('karyawan')->where('nik', $nik)->first();
        if ($request->hasFile('foto')) {
            $foto = $nik . "." . $request->file('foto')->getClientOriginalExtension();
        } else {
            $foto = $karyawan->foto;
        }
        if (empty($request->password)) {
            $data = [
                'nama_lengkap' => $nama_lengkap,
                'no_hp' => $no_hp,
                'foto' => $foto
            ];
        } else {
            $data = [
                'nama_lengkap' => $nama_lengkap,
                'no_hp' => $no_hp,
                'password' => $password,
                'foto' => $foto
            ];
        }

        try {
            DB::table('karyawan')->where('nik', $nik)->update($data);
            if ($request->hasFile('foto')) {
                $folderPath = "public/uploads/karyawan/";
                $request->file('foto')->storeAs($folderPath, $foto);
            }
            return Redirect::back()->with(['success' => 'Data Berhasil Di Update']);
        } catch (\Exception $e) {
            return Redirect::back()->with(['error' => 'Data gagal Di Update']);
        }
    }


    public function histori()
    {
        $namabulan = ["", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
        return view('presensi.histori', compact('namabulan'));
    }

    public function gethistori(Request $request)
    {
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        $dari = $tahun . "-" . $bulan . "-01";
        $sampai = date("Y-m-t", strtotime($dari));
        $nik = Auth::guard('karyawan')->user()->nik;

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
            ->whereBetween('presensi.tgl_presensi', [$dari, $sampai])
            ->orderBy('presensi.tgl_presensi', 'desc')
            ->get();



        //dd($histori);

        return view('presensi.gethistori', $data);
    }

    public function izin(Request $request)
    {
        $nik = Auth::guard('karyawan')->user()->nik;

        //Izin terlambat
        $qizinterlambat = Izinterlambat::query();
        $qizinterlambat->where('nik', $nik);
        if (!empty($request->bulan)) {
            $qizinterlambat->whereRaw('MONTH(tanggal)="' . $request->bulan . '"');
        }

        if (!empty($request->tahun)) {
            $qizinterlambat->whereRaw('YEAR(tanggal)="' . $request->tahun . '"');
        }
        $qizinterlambat->orderBy('tanggal', 'desc');
        $qizinterlambat->limit(7);
        $data['izinterlambat'] = $qizinterlambat->get();


        $qizinabsen = Izinabsen::query();
        $qizinabsen->where('nik', $nik);
        if (!empty($request->bulan)) {
            $qizinabsen->whereRaw('MONTH(tanggal)="' . $request->bulan . '"');
        }

        if (!empty($request->tahun)) {
            $qizinabsen->whereRaw('YEAR(tanggal)="' . $request->tahun . '"');
        }
        $qizinabsen->orderBy('tanggal', 'desc');
        $qizinabsen->limit(7);
        $data['izinabsen'] = $qizinabsen->get();


        $qizinsakit = Izinsakit::query();
        $qizinsakit->where('nik', $nik);
        if (!empty($request->bulan)) {
            $qizinsakit->whereRaw('MONTH(tanggal)="' . $request->bulan . '"');
        }

        if (!empty($request->tahun)) {
            $qizinsakit->whereRaw('YEAR(tanggal)="' . $request->tahun . '"');
        }
        $qizinsakit->orderBy('tanggal', 'desc');
        $qizinsakit->limit(7);
        $data['izinsakit'] = $qizinsakit->get();


        $qizincuti = Izincuti::query();
        $qizincuti->where('nik', $nik);
        if (!empty($request->bulan)) {
            $qizincuti->whereRaw('MONTH(tanggal)="' . $request->bulan . '"');
        }

        if (!empty($request->tahun)) {
            $qizincuti->whereRaw('YEAR(tanggal)="' . $request->tahun . '"');
        }
        $qizincuti->orderBy('tanggal', 'desc');
        $qizincuti->limit(7);
        $data['izincuti'] = $qizincuti->get();

        $qizinkeluar = Izinkeluarkantor::query();
        $qizinkeluar->where('nik', $nik);
        if (!empty($request->bulan)) {
            $qizinkeluar->whereRaw('MONTH(tanggal)="' . $request->bulan . '"');
        }

        if (!empty($request->tahun)) {
            $qizinkeluar->whereRaw('YEAR(tanggal)="' . $request->tahun . '"');
        }
        $qizinkeluar->orderBy('tanggal', 'desc');
        $qizinkeluar->limit(7);
        $data['izinkeluar'] = $qizinkeluar->get();


        $qizinpulang = Izinpulang::query();
        $qizinpulang->where('nik', $nik);
        if (!empty($request->bulan)) {
            $qizinpulang->whereRaw('MONTH(tanggal)="' . $request->bulan . '"');
        }

        if (!empty($request->tahun)) {
            $qizinpulang->whereRaw('YEAR(tanggal)="' . $request->tahun . '"');
        }
        $qizinpulang->orderBy('tanggal', 'desc');
        $qizinpulang->limit(7);
        $data['izinpulang'] = $qizinpulang->get();

        $tahun_ini = date("Y");
        $hak_cuti = DB::table('hrd_hak_cuti')
            ->where('nik', $nik)
            ->where('kode_cuti', 'C01')
            ->where('tahun', $tahun_ini)
            ->first();
        
        if ($hak_cuti) {
            $data['jatah_cuti'] = $hak_cuti->jml_hari;
        } else {
            $cuti_tahunan = DB::table('hrd_jeniscuti')->where('kode_cuti', 'C01')->first();
            $data['jatah_cuti'] = $cuti_tahunan != null ? $cuti_tahunan->jml_hari : 12;
        }

        $data['cuti_terpakai'] = DB::table('pengajuan_izin')
            ->where('nik', $nik)
            ->where('status', 'c')
            ->where('status_approved', 1)
            ->where('jenis_cuti', 'C01')
            ->whereYear('tgl_izin', $tahun_ini)
            ->sum('jmlhari');

        $data['sisa_cuti'] = max(0, $data['jatah_cuti'] - $data['cuti_terpakai']);

        $data['namabulan'] = ["", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
        return view('presensi.izin', $data);
    }

    public function buatizin()
    {

        $mastercuti = [];
        return view('presensi.buatizin', compact('mastercuti'));
    }

    public function storeizin(Request $request)
    {
        $nik = Auth::guard('karyawan')->user()->nik;
        $dari = $request->jenis_izin == "PL" || $request->jenis_izin == "KL" ? date("Y-m-d") : $request->dari;
        $sampai =  $request->jenis_izin == "PL" || $request->jenis_izin == "KL" ? date("Y-m-d") : $request->sampai;
        $jmlhari = $request->jmlhari;
        $status = $request->status;
        $keterangan = $request->keterangan;
        $jenis_izin = $request->jenis_izin;
        $jam_pulang = $request->jam_pulang;
        $jam_keluar = $request->jam_keluar;
        $jenis_cuti = $request->jenis_cuti;
        $tgl = explode("-", $dari);
        $tahun = substr($tgl[0], 2, 2);
        $izin = DB::table("pengajuan_izin")
            ->whereRaw('YEAR(dari)="' . $tgl[0] . '"')
            ->orderBy("kode_izin", "desc")
            ->first();

        $last_kodeizin = $izin != null ? $izin->kode_izin : '';
        $kode_izin  = $this->buatkode($last_kodeizin, "IZ" . $tahun, 3);
        if ($request->hasFile('sid')) {
            $sid = $kode_izin . "." . $request->file('sid')->getClientOriginalExtension();
        } else {
            $sid = null;
        }
        $data = [
            'kode_izin' => $kode_izin,
            'nik' => $nik,
            'dari' => $dari,
            'sampai' => $sampai,
            'jmlhari' => $jmlhari,
            'status' => $status,
            'keterangan' => $keterangan,
            'sid' => $sid,
            'jenis_izin' => $jenis_izin,
            'jam_pulang' => $jam_pulang,
            'jam_keluar' => $jam_keluar,
            'jenis_cuti' => $jenis_cuti,
            'created_by' => 'user'
        ];

        try {
            $simpan = DB::table('pengajuan_izin')->insert($data);
            if ($simpan) {
                if ($request->hasFile('sid')) {
                    $folderPath = "public/uploads/sid/";
                    $request->file('sid')->storeAs($folderPath, $sid);
                }
            }
            return redirect('/presensi/izin')->with(['success' => 'Data Berhasil Disimpan']);
        } catch (\Exception $e) {
            dd($e);
            return redirect('/presensi/izin')->with(['error' => 'Data Gagal Disimpan']);
        }
    }

    public function monitoring()
    {
        return view('presensi.monitoring');
    }

    public function getpresensi(Request $request)
    {
        $tanggal = $request->tanggal;
        $presensi = DB::table('presensi')
            ->select('presensi.*', 'nama_lengkap', 'nama_dept', 'jam_masuk')
            ->join('karyawan', 'presensi.nik', '=', 'karyawan.nik')
            ->join('departemen', 'karyawan.kode_dept', '=', 'departemen.kode_dept')
            ->leftJoin('jam_kerja', 'presensi.kode_jam_kerja', '=', 'jam_kerja.kode_jam_kerja')
            ->where('tgl_presensi', $tanggal)
            ->get();

        return view('presensi.getpresensi', compact('presensi'));
    }

    public function tampilkanpeta(Request $request)
    {
        $id = $request->id;
        $presensi = DB::table('presensi')->where('id', $id)
            ->join('karyawan', 'presensi.nik', '=', 'karyawan.nik')
            ->first();
        return view('presensi.showmap', compact('presensi'));
    }


    public function laporan()
    {
        $namabulan = ["", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
        $karyawan = DB::table('karyawan')->orderBy('nama_lengkap')->get();
        return view('presensi.laporan', compact('namabulan', 'karyawan'));
    }

    public function cetaklaporan(Request $request)
    {
        $nik = $request->nik;
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        $namabulan = ["", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
        $karyawan = DB::table('karyawan')->where('nik', $nik)
            ->join('departemen', 'karyawan.kode_dept', '=', 'departemen.kode_dept')
            ->first();

        $presensi = DB::table('presensi')
            ->select('presensi.*', 'jam_masuk')
            ->leftJoin('jam_kerja', 'presensi.kode_jam_kerja', '=', 'jam_kerja.kode_jam_kerja')
            ->where('nik', $nik)
            ->whereRaw('MONTH(tgl_presensi)="' . $bulan . '"')
            ->whereRaw('YEAR(tgl_presensi)="' . $tahun . '"')
            ->orderBy('tgl_presensi')
            ->get();

        $lembur = DB::table('hrd_lembur_detail')
            ->join('hrd_lembur', 'hrd_lembur_detail.kode_lembur', '=', 'hrd_lembur.kode_lembur')
            ->where('hrd_lembur_detail.nik', $nik)
            ->whereMonth('hrd_lembur.tanggal', $bulan)
            ->whereYear('hrd_lembur.tanggal', $tahun)
            ->where('hrd_lembur.status', 1) // Approved
            ->select('hrd_lembur.tanggal', 'hrd_lembur.tanggal_dari', 'hrd_lembur.tanggal_sampai')
            ->get()
            ->keyBy('tanggal');

        if (isset($_POST['exportexcel'])) {
            $time = date("d-M-Y");
            // Fungsi header dengan mengirimkan raw data excel
            header("Content-type: application/vnd-ms-excel");
            // Mendefinisikan nama file ekspor "hasil-export.xls"
            header("Content-Disposition: attachment; filename=Laporan Presensi Karyawan $time.xls");
            return view('presensi.cetaklaporanexcel', compact('bulan', 'tahun', 'namabulan', 'karyawan', 'presensi', 'lembur'));
        }
        return view('presensi.cetaklaporan', compact('bulan', 'tahun', 'namabulan', 'karyawan', 'presensi', 'lembur'));
    }

    public function laporanlembur()
    {
        $namabulan = ["", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
        $karyawan = DB::table('karyawan')->orderBy('nama_lengkap')->get();
        return view('presensi.laporanlembur', compact('namabulan', 'karyawan'));
    }

    public function cetaklaporanlembur(Request $request)
    {
        $nik = $request->nik;
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        $namabulan = ["", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];

        $query = DB::table('hrd_lembur_detail')
            ->join('hrd_lembur', 'hrd_lembur_detail.kode_lembur', '=', 'hrd_lembur.kode_lembur')
            ->join('karyawan', 'hrd_lembur_detail.nik', '=', 'karyawan.nik')
            ->leftJoin('presensi', function ($join) {
                $join->on('hrd_lembur_detail.nik', '=', 'presensi.nik')
                    ->on('hrd_lembur.tanggal', '=', 'presensi.tgl_presensi');
            })
            ->whereMonth('hrd_lembur.tanggal', $bulan)
            ->whereYear('hrd_lembur.tanggal', $tahun)
            ->where('hrd_lembur.status', 1); // Approved

        if (!empty($nik)) {
            $query->where('hrd_lembur_detail.nik', $nik);
            $karyawan_selected = DB::table('karyawan')->where('nik', $nik)
                ->join('departemen', 'karyawan.kode_dept', '=', 'departemen.kode_dept')
                ->first();
        } else {
            $karyawan_selected = null;
        }

        $lembur = $query->select(
            'hrd_lembur_detail.nik',
            'karyawan.nama_lengkap',
            'hrd_lembur.tanggal',
            'hrd_lembur.tanggal_dari',
            'hrd_lembur.tanggal_sampai',
            'hrd_lembur.keterangan',
            'presensi.jam_in',
            'presensi.jam_out'
        )->orderBy('hrd_lembur.tanggal')->get();

        if (isset($_POST['exportexcel'])) {
            $time = date("d-M-Y");
            // Fungsi header dengan mengirimkan raw data excel
            header("Content-type: application/vnd-ms-excel");
            // Mendefinisikan nama file ekspor "hasil-export.xls"
            header("Content-Disposition: attachment; filename=Laporan Lembur Karyawan $time.xls");
            return view('presensi.cetaklaporanlemburexcel', compact('bulan', 'tahun', 'namabulan', 'lembur', 'karyawan_selected', 'nik'));
        }
        return view('presensi.cetaklaporanlembur', compact('bulan', 'tahun', 'namabulan', 'lembur', 'karyawan_selected', 'nik'));
    }

    public function laporancuti()
    {
        $karyawan = DB::table('karyawan')->orderBy('nama_lengkap')->get();
        return view('presensi.laporancuti', compact('karyawan'));
    }

    public function cetaklaporancuti(Request $request)
    {
        $nik = $request->nik;
        $tahun = $request->tahun;

        $query = DB::table('pengajuan_izin')
            ->join('karyawan', 'pengajuan_izin.nik', '=', 'karyawan.nik')
            ->join('hrd_jeniscuti', 'pengajuan_izin.jenis_cuti', '=', 'hrd_jeniscuti.kode_cuti')
            ->where('pengajuan_izin.status', 'c')
            ->where('pengajuan_izin.status_approved', 1)
            ->whereYear('pengajuan_izin.tgl_izin', $tahun);

        if (!empty($nik)) {
            $query->where('pengajuan_izin.nik', $nik);
            $karyawan_selected = DB::table('karyawan')->where('nik', $nik)
                ->join('departemen', 'karyawan.kode_dept', '=', 'departemen.kode_dept')
                ->first();
        } else {
            $karyawan_selected = null;
        }

        $cuti = $query->select(
            'pengajuan_izin.*',
            'karyawan.nama_lengkap',
            'hrd_jeniscuti.nama_cuti'
        )->orderBy('pengajuan_izin.tgl_izin')->get();

        if (isset($_POST['exportexcel'])) {
            $time = date("d-M-Y");
            header("Content-type: application/vnd-ms-excel");
            header("Content-Disposition: attachment; filename=Laporan Cuti Karyawan Per Tahun $time.xls");
            return view('presensi.cetaklaporancutiexcel', compact('tahun', 'cuti', 'karyawan_selected', 'nik'));
        }
        return view('presensi.cetaklaporancuti', compact('tahun', 'cuti', 'karyawan_selected', 'nik'));
    }

    public function rekap()
    {
        $namabulan = ["", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
        return view('presensi.rekap', compact('namabulan'));
    }

    public function cetakrekap(Request $request)
    {
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        $namabulan = ["", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
        $rekap = DB::table('presensi')
            ->selectRaw('presensi.nik,nama_lengkap,
                MAX(IF(DAY(tgl_presensi) = 1,CONCAT(jam_in,"-",IFNULL(jam_out,"00:00:00"),"-",IFNULL(jam_masuk,"07:00:00"),"-",IFNULL(jam_pulang,"17:00:00")),"")) as tgl_1,
                MAX(IF(DAY(tgl_presensi) = 2,CONCAT(jam_in,"-",IFNULL(jam_out,"00:00:00"),"-",IFNULL(jam_masuk,"07:00:00"),"-",IFNULL(jam_pulang,"17:00:00")),"")) as tgl_2,
                MAX(IF(DAY(tgl_presensi) = 3,CONCAT(jam_in,"-",IFNULL(jam_out,"00:00:00"),"-",IFNULL(jam_masuk,"07:00:00"),"-",IFNULL(jam_pulang,"17:00:00")),"")) as tgl_3,
                MAX(IF(DAY(tgl_presensi) = 4,CONCAT(jam_in,"-",IFNULL(jam_out,"00:00:00"),"-",IFNULL(jam_masuk,"07:00:00"),"-",IFNULL(jam_pulang,"17:00:00")),"")) as tgl_4,
                MAX(IF(DAY(tgl_presensi) = 5,CONCAT(jam_in,"-",IFNULL(jam_out,"00:00:00"),"-",IFNULL(jam_masuk,"07:00:00"),"-",IFNULL(jam_pulang,"17:00:00")),"")) as tgl_5,
                MAX(IF(DAY(tgl_presensi) = 6,CONCAT(jam_in,"-",IFNULL(jam_out,"00:00:00"),"-",IFNULL(jam_masuk,"07:00:00"),"-",IFNULL(jam_pulang,"17:00:00")),"")) as tgl_6,
                MAX(IF(DAY(tgl_presensi) = 7,CONCAT(jam_in,"-",IFNULL(jam_out,"00:00:00"),"-",IFNULL(jam_masuk,"07:00:00"),"-",IFNULL(jam_pulang,"17:00:00")),"")) as tgl_7,
                MAX(IF(DAY(tgl_presensi) = 8,CONCAT(jam_in,"-",IFNULL(jam_out,"00:00:00"),"-",IFNULL(jam_masuk,"07:00:00"),"-",IFNULL(jam_pulang,"17:00:00")),"")) as tgl_8,
                MAX(IF(DAY(tgl_presensi) = 9,CONCAT(jam_in,"-",IFNULL(jam_out,"00:00:00"),"-",IFNULL(jam_masuk,"07:00:00"),"-",IFNULL(jam_pulang,"17:00:00")),"")) as tgl_9,
                MAX(IF(DAY(tgl_presensi) = 10,CONCAT(jam_in,"-",IFNULL(jam_out,"00:00:00"),"-",IFNULL(jam_masuk,"07:00:00"),"-",IFNULL(jam_pulang,"17:00:00")),"")) as tgl_10,
                MAX(IF(DAY(tgl_presensi) = 11,CONCAT(jam_in,"-",IFNULL(jam_out,"00:00:00"),"-",IFNULL(jam_masuk,"07:00:00"),"-",IFNULL(jam_pulang,"17:00:00")),"")) as tgl_11,
                MAX(IF(DAY(tgl_presensi) = 12,CONCAT(jam_in,"-",IFNULL(jam_out,"00:00:00"),"-",IFNULL(jam_masuk,"07:00:00"),"-",IFNULL(jam_pulang,"17:00:00")),"")) as tgl_12,
                MAX(IF(DAY(tgl_presensi) = 13,CONCAT(jam_in,"-",IFNULL(jam_out,"00:00:00"),"-",IFNULL(jam_masuk,"07:00:00"),"-",IFNULL(jam_pulang,"17:00:00")),"")) as tgl_13,
                MAX(IF(DAY(tgl_presensi) = 14,CONCAT(jam_in,"-",IFNULL(jam_out,"00:00:00"),"-",IFNULL(jam_masuk,"07:00:00"),"-",IFNULL(jam_pulang,"17:00:00")),"")) as tgl_14,
                MAX(IF(DAY(tgl_presensi) = 15,CONCAT(jam_in,"-",IFNULL(jam_out,"00:00:00"),"-",IFNULL(jam_masuk,"07:00:00"),"-",IFNULL(jam_pulang,"17:00:00")),"")) as tgl_15,
                MAX(IF(DAY(tgl_presensi) = 16,CONCAT(jam_in,"-",IFNULL(jam_out,"00:00:00"),"-",IFNULL(jam_masuk,"07:00:00"),"-",IFNULL(jam_pulang,"17:00:00")),"")) as tgl_16,
                MAX(IF(DAY(tgl_presensi) = 17,CONCAT(jam_in,"-",IFNULL(jam_out,"00:00:00"),"-",IFNULL(jam_masuk,"07:00:00"),"-",IFNULL(jam_pulang,"17:00:00")),"")) as tgl_17,
                MAX(IF(DAY(tgl_presensi) = 18,CONCAT(jam_in,"-",IFNULL(jam_out,"00:00:00"),"-",IFNULL(jam_masuk,"07:00:00"),"-",IFNULL(jam_pulang,"17:00:00")),"")) as tgl_18,
                MAX(IF(DAY(tgl_presensi) = 19,CONCAT(jam_in,"-",IFNULL(jam_out,"00:00:00"),"-",IFNULL(jam_masuk,"07:00:00"),"-",IFNULL(jam_pulang,"17:00:00")),"")) as tgl_19,
                MAX(IF(DAY(tgl_presensi) = 20,CONCAT(jam_in,"-",IFNULL(jam_out,"00:00:00"),"-",IFNULL(jam_masuk,"07:00:00"),"-",IFNULL(jam_pulang,"17:00:00")),"")) as tgl_20,
                MAX(IF(DAY(tgl_presensi) = 21,CONCAT(jam_in,"-",IFNULL(jam_out,"00:00:00"),"-",IFNULL(jam_masuk,"07:00:00"),"-",IFNULL(jam_pulang,"17:00:00")),"")) as tgl_21,
                MAX(IF(DAY(tgl_presensi) = 22,CONCAT(jam_in,"-",IFNULL(jam_out,"00:00:00"),"-",IFNULL(jam_masuk,"07:00:00"),"-",IFNULL(jam_pulang,"17:00:00")),"")) as tgl_22,
                MAX(IF(DAY(tgl_presensi) = 23,CONCAT(jam_in,"-",IFNULL(jam_out,"00:00:00"),"-",IFNULL(jam_masuk,"07:00:00"),"-",IFNULL(jam_pulang,"17:00:00")),"")) as tgl_23,
                MAX(IF(DAY(tgl_presensi) = 24,CONCAT(jam_in,"-",IFNULL(jam_out,"00:00:00"),"-",IFNULL(jam_masuk,"07:00:00"),"-",IFNULL(jam_pulang,"17:00:00")),"")) as tgl_24,
                MAX(IF(DAY(tgl_presensi) = 25,CONCAT(jam_in,"-",IFNULL(jam_out,"00:00:00"),"-",IFNULL(jam_masuk,"07:00:00"),"-",IFNULL(jam_pulang,"17:00:00")),"")) as tgl_25,
                MAX(IF(DAY(tgl_presensi) = 26,CONCAT(jam_in,"-",IFNULL(jam_out,"00:00:00"),"-",IFNULL(jam_masuk,"07:00:00"),"-",IFNULL(jam_pulang,"17:00:00")),"")) as tgl_26,
                MAX(IF(DAY(tgl_presensi) = 27,CONCAT(jam_in,"-",IFNULL(jam_out,"00:00:00"),"-",IFNULL(jam_masuk,"07:00:00"),"-",IFNULL(jam_pulang,"17:00:00")),"")) as tgl_27,
                MAX(IF(DAY(tgl_presensi) = 28,CONCAT(jam_in,"-",IFNULL(jam_out,"00:00:00"),"-",IFNULL(jam_masuk,"07:00:00"),"-",IFNULL(jam_pulang,"17:00:00")),"")) as tgl_28,
                MAX(IF(DAY(tgl_presensi) = 29,CONCAT(jam_in,"-",IFNULL(jam_out,"00:00:00"),"-",IFNULL(jam_masuk,"07:00:00"),"-",IFNULL(jam_pulang,"17:00:00")),"")) as tgl_29,
                MAX(IF(DAY(tgl_presensi) = 30,CONCAT(jam_in,"-",IFNULL(jam_out,"00:00:00"),"-",IFNULL(jam_masuk,"07:00:00"),"-",IFNULL(jam_pulang,"17:00:00")),"")) as tgl_30,
                MAX(IF(DAY(tgl_presensi) = 31,CONCAT(jam_in,"-",IFNULL(jam_out,"00:00:00"),"-",IFNULL(jam_masuk,"07:00:00"),"-",IFNULL(jam_pulang,"17:00:00")),"")) as tgl_31')
            ->join('karyawan', 'presensi.nik', '=', 'karyawan.nik')
            ->leftJoin('jam_kerja', 'presensi.kode_jam_kerja', '=', 'jam_kerja.kode_jam_kerja')
            ->whereRaw('MONTH(tgl_presensi)="' . $bulan . '"')
            ->whereRaw('YEAR(tgl_presensi)="' . $tahun . '"')
            ->groupByRaw('presensi.nik,nama_lengkap')
            ->orderBy('nama_lengkap')
            ->get();

        $rekap_lembur = DB::table('hrd_lembur_detail')
            ->join('hrd_lembur', 'hrd_lembur_detail.kode_lembur', '=', 'hrd_lembur.kode_lembur')
            ->join('presensi', function($join) {
                $join->on('hrd_lembur_detail.nik', '=', 'presensi.nik')
                    ->on('hrd_lembur.tanggal', '=', 'presensi.tgl_presensi');
            })
            ->whereMonth('hrd_lembur.tanggal', $bulan)
            ->whereYear('hrd_lembur.tanggal', $tahun)
            ->where('hrd_lembur.status', 1) // Approved
            ->select('hrd_lembur_detail.nik', 'hrd_lembur.tanggal', 'presensi.jam_in', 'presensi.jam_out', 'hrd_lembur.tanggal_dari', 'hrd_lembur.tanggal_sampai')
            ->get();
            
        $total_lembur = [];
        foreach ($rekap_lembur as $l) {
            if (!empty($l->jam_in) && !empty($l->jam_out)) {
                $presence_start = strtotime($l->tanggal . ' ' . $l->jam_in);
                $presence_end = strtotime($l->tanggal . ' ' . $l->jam_out);
                if ($presence_end < $presence_start) {
                    $presence_end += 86400; // Crossover day
                }
                
                $ot_start = strtotime($l->tanggal_dari);
                $ot_end = strtotime($l->tanggal_sampai);
                if ($ot_end < $ot_start) {
                    $ot_end += 86400;
                }
                
                $intersect_start = max($presence_start, $ot_start);
                $intersect_end = min($presence_end, $ot_end);
                
                if ($intersect_start < $intersect_end) {
                    $diff = ($intersect_end - $intersect_start) / 3600;
                    if (!isset($total_lembur[$l->nik])) {
                        $total_lembur[$l->nik] = 0;
                    }
                    $total_lembur[$l->nik] += $diff;
                }
            }
        }

        if (isset($_POST['exportexcel'])) {
            $time = date("d-M-Y");
            // Fungsi header dengan mengirimkan raw data excel
            header("Content-type: application/vnd-ms-excel");
            // Mendefinisikan nama file ekspor "hasil-export.xls"
            header("Content-Disposition: attachment; filename=Rekap Presensi Karyawan $time.xls");
        }
        return view('presensi.cetakrekap', compact('bulan', 'tahun', 'namabulan', 'rekap', 'total_lembur'));
    }

    public function izinsakit(Request $request)
    {

        $query = Pengajuanizin::query();
        $query->select('id', 'tgl_izin', 'pengajuan_izin.nik', 'nama_lengkap', 'jabatan', 'status', 'status_approved', 'keterangan', 'jenis_izin');
        $query->join('karyawan', 'pengajuan_izin.nik', '=', 'karyawan.nik');
        if (!empty($request->dari) && !empty($request->sampai)) {
            $query->whereBetween('tgl_izin', [$request->dari, $request->sampai]);
        }

        if (!empty($request->nik)) {
            $query->where('pengajuan_izin.nik', $request->nik);
        }

        if (!empty($request->nama_lengkap)) {
            $query->where('nama_lengkap', 'like', '%' . $request->nama_lengkap . '%');
        }

        if ($request->status_approved === '0' || $request->status_approved === '1' || $request->status_approved === '2') {
            $query->where('status_approved', $request->status_approved);
        }
        $query->orderBy('tgl_izin', 'desc');
        $izinsakit = $query->paginate(2);
        $izinsakit->appends($request->all());
        return view('presensi.izinsakit', compact('izinsakit'));
    }

    public function approveizinsakit(Request $request)
    {
        $status_approved = $request->status_approved;
        $id_izinsakit_form = $request->id_izinsakit_form;
        
        $izin = DB::table('pengajuan_izin')->where('id', $id_izinsakit_form)->first();
        if ($izin) {
            $update = DB::table('pengajuan_izin')->where('id', $id_izinsakit_form)->update([
                'status_approved' => $status_approved
            ]);
            
            // Sync status to specific tables
            $kode = $izin->kode_izin;
            if (str_starts_with($kode, 'IA')) {
                DB::table('hrd_izinabsen')->where('kode_izin', $kode)->update(['status' => $status_approved]);
            } elseif (str_starts_with($kode, 'IS')) {
                DB::table('hrd_izinsakit')->where('kode_izin_sakit', $kode)->update(['status' => $status_approved]);
            } elseif (str_starts_with($kode, 'IC')) {
                DB::table('hrd_izincuti')->where('kode_izin_cuti', $kode)->update(['status' => $status_approved]);
            } elseif (str_starts_with($kode, 'IT')) {
                DB::table('hrd_izinterlambat')->where('kode_izin_terlambat', $kode)->update(['status' => $status_approved]);
            } elseif (str_starts_with($kode, 'IK')) {
                DB::table('hrd_izinkeluar')->where('kode_izin_keluar', $kode)->update(['status' => $status_approved]);
            } elseif (str_starts_with($kode, 'IP')) {
                DB::table('hrd_izinpulang')->where('kode_izin_pulang', $kode)->update(['status_approved' => $status_approved]);
            }
            
            if ($update) {
                return Redirect::back()->with(['success' => 'Data Berhasil Di Update']);
            }
        }
        return Redirect::back()->with(['warning' => 'Data Gagal Di Update']);
    }

    public function batalkanizinsakit($id)
    {
        $izin = DB::table('pengajuan_izin')->where('id', $id)->first();
        if ($izin) {
            $update = DB::table('pengajuan_izin')->where('id', $id)->update([
                'status_approved' => 0
            ]);
            
            // Sync status to specific tables
            $kode = $izin->kode_izin;
            if (str_starts_with($kode, 'IA')) {
                DB::table('hrd_izinabsen')->where('kode_izin', $kode)->update(['status' => 0]);
            } elseif (str_starts_with($kode, 'IS')) {
                DB::table('hrd_izinsakit')->where('kode_izin_sakit', $kode)->update(['status' => 0]);
            } elseif (str_starts_with($kode, 'IC')) {
                DB::table('hrd_izincuti')->where('kode_izin_cuti', $kode)->update(['status' => 0]);
            } elseif (str_starts_with($kode, 'IT')) {
                DB::table('hrd_izinterlambat')->where('kode_izin_terlambat', $kode)->update(['status' => 0]);
            } elseif (str_starts_with($kode, 'IK')) {
                DB::table('hrd_izinkeluar')->where('kode_izin_keluar', $kode)->update(['status' => 0]);
            } elseif (str_starts_with($kode, 'IP')) {
                DB::table('hrd_izinpulang')->where('kode_izin_pulang', $kode)->update(['status_approved' => 0]);
            }
            
            if ($update) {
                return Redirect::back()->with(['success' => 'Data Berhasil Di Update']);
            }
        }
        return Redirect::back()->with(['warning' => 'Data Gagal Di Update']);
    }

    public function cekpengajuanizin(Request $request)
    {
        $tgl_izin = $request->tgl_izin;
        $nik = Auth::guard('karyawan')->user()->nik;

        $cek = DB::table('pengajuan_izin')->where('nik', $nik)->where('tgl_izin', $tgl_izin)->count();
        return $cek;
    }

    public function showactizin($id)
    {
        $izin = DB::table('pengajuan_izin')->where('kode_izin', $id)->first();
        return view('presensi.showactizin', compact('id', 'izin'));
    }

    public function showsid($id)
    {
        $izin = DB::table('pengajuan_izin')->where('kode_izin', $id)->first();
        return view('presensi.showsid', compact('id', 'izin'));
    }

    public function editizin($id)
    {
        $izin = DB::table('pengajuan_izin')->where('kode_izin', $id)->first();
        return view('presensi.editizin', compact('izin'));
    }

    public function updateizin($id, Request $request)
    {

        $dari = $request->dari;
        $sampai = $request->sampai;
        $jmlhari = $request->jmlhari;
        $status = $request->status;
        $keterangan = $request->keterangan;



        $data = [
            'dari' => $dari,
            'sampai' => $sampai,
            'jmlhari' => $jmlhari,
            'status' => $status,
            'keterangan' => $keterangan
        ];

        try {
            DB::table('pengajuan_izin')->where('kode_izin', $id)->update($data);
            return redirect('/presensi/izin')->with(['success' => 'Data Berhasil Disimpan']);
        } catch (\Exception $e) {
            return redirect('/presensi/izin')->with(['error' => 'Data Gagal Disimpan']);
        }
    }

    public function deleteizin($id)
    {
        try {
            DB::table('pengajuan_izin')->where('kode_izin', $id)->delete();
            
            // Delete from specific tables as well
            if (str_starts_with($id, 'IA')) {
                DB::table('hrd_izinabsen')->where('kode_izin', $id)->delete();
            } elseif (str_starts_with($id, 'IS')) {
                DB::table('hrd_izinsakit')->where('kode_izin_sakit', $id)->delete();
            } elseif (str_starts_with($id, 'IC')) {
                DB::table('hrd_izincuti')->where('kode_izin_cuti', $id)->delete();
            } elseif (str_starts_with($id, 'IT')) {
                DB::table('hrd_izinterlambat')->where('kode_izin_terlambat', $id)->delete();
            } elseif (str_starts_with($id, 'IK')) {
                DB::table('hrd_izinkeluar')->where('kode_izin_keluar', $id)->delete();
            } elseif (str_starts_with($id, 'IP')) {
                DB::table('hrd_izinpulang')->where('kode_izin_pulang', $id)->delete();
            }

            return redirect('/presensi/izin')->with(['success' => 'Data Berhasil Dihapus']);
        } catch (\Exception $e) {
            return redirect('/presensi/izin')->with(['error' => 'Data Gagal Dihapus']);
        }
    }

    public function storefrommachine()
    {


        $original_data  = file_get_contents('php://input');
        $decoded_data   = json_decode($original_data, true);
        $encoded_data   = json_encode($decoded_data);

        $data           = $decoded_data['data'];
        $pin            = $data['pin'];
        $status_scan    = $data['status_scan'];
        $scan           = $data['scan'];


        // $nik               = '21.02.232';


        $tgl_presensi   = date("Y-m-d", strtotime($scan));
        $karyawan       = DB::table('master_karyawan')->where('pin', $pin)->first();
        $jabatan        = (object)['nama_jabatan' => $karyawan->jabatan, 'kategori' => ''];

        if ($karyawan == null) {
            echo "PIN Tidak Ditemukan";
            $nik = "";
        } else {
            $nik = $karyawan->nik;
        }
        $cekperjalanandinas = DB::table('pengajuan_izin')
            ->where('status', 'p')
            ->whereRaw('"' . $tgl_presensi . '" >= dari')
            ->whereRaw('"' . $tgl_presensi . '" <= sampai')
            ->where('nik', $nik)
            ->first();
        if ($cekperjalanandinas != null) {
            $kode_cabang = $cekperjalanandinas->kode_cabang;
        } else {
            $kode_cabang = $karyawan->id_kantor;
        }
        $lastday = date('Y-m-d', strtotime('-1 day', strtotime($tgl_presensi)));

        $jam = $scan;

        $cekjadwalshift = DB::table('konfigurasi_jadwalkerja_detail')
            ->join('konfigurasi_jadwalkerja', 'konfigurasi_jadwalkerja_detail.kode_setjadwal', '=', 'konfigurasi_jadwalkerja.kode_setjadwal')
            ->whereRaw('"' . $tgl_presensi . '" >= dari')
            ->whereRaw('"' . $tgl_presensi . '" <= sampai')
            ->where('nik', $nik)
            ->first();

        $cekgantishift = null;

        if ($cekgantishift != null) {
            $kode_jadwal = $cekgantishift->kode_jadwal;
        } else if ($cekjadwalshift != null) {
            $kode_jadwal = $cekjadwalshift->kode_jadwal;
        } else if ($cekperjalanandinas != null) {
            $cekjadwaldinas = DB::table('jadwal_kerja')
                ->where('nama_jadwal', 'NON SHIFT')
                ->where('kode_cabang', $cekperjalanandinas->kode_cabang)->first();
            $kode_jadwal = $cekjadwaldinas->kode_jadwal;
        } else {
            $kode_jadwal = $karyawan->kode_jadwal ?? 'JD01';
        }

        $ceklibur = 0;
        $datalibur = null;
        $tanggal_libur = '';

        $ceklembur = 0;

        if ($ceklibur > 0 && $this->hari_tanggal($tanggal_libur) == "Sabtu") {
            $hariini = "Sabtu";
        } else {
            $hariini = $this->hari_ini();
        }


        if ($jabatan->nama_jabatan == "SECURITY" && $hariini == "Sabtu") {
            $hariini = "Senin";
        }

        if ($ceklembur > 0 && $hariini == "Sabtu") {
            $hariini = "Jumat";
        }

        $id_group = $karyawan->grup;
        $group_saus =  [29, 26, 27];
        if (date('Y-m-d') == '2024-02-10') {
            if (in_array($id_group, $group_saus)) {
                $hariini = "Senin";
            }
        }

        $jadwal = $this->getJamKerjaKaryawan($nik, $tgl_presensi, $hariini);


        $jam_kerja = DB::table('jam_kerja')->where('kode_jam_kerja', $jadwal->kode_jam_kerja)->first();
        $lintashari  = $jam_kerja->lintashari;

        $cek = DB::table('presensi')->where('tgl_presensi', $tgl_presensi)->where('nik', $nik)->first();

        $jam_sekarang = date("H:i:s");


        if ($status_scan == 0) {
            $jam_masuk = $tgl_presensi . " " . "10:00";
            $jamabsen = $jam;
            if ($kode_jadwal == "JD004" && $jamabsen <= $jam_masuk  || $kode_jadwal == "JD003" && $jamabsen <= $jam_masuk) {
                echo "error|Maaf Belum Waktunya Absen Masuk|in";
            } else {
                if ($cek != null && !empty($cek->jam_in)) {
                    echo "error|Maaf Gagal absen, Anda Sudah Melakukan Presensi Masuk|in";
                } else if ($cek != null && empty($cek->jam_in)) {
                    $data_masuk = [
                        'jam_in' => $jam
                    ];
                    $update = DB::table('presensi')->where('tgl_presensi', $tgl_presensi)->where('nik', $nik)->update($data_masuk);
                    if ($update) {
                        echo "success|Terimkasih, Selamat Bekerja|in";
                    } else {
                        echo "error|Maaf Gagal absen, Hubungi Tim It|in";
                    }
                } else if ($cek == null) {
                    $data = [
                        'nik' => $nik,
                        'tgl_presensi' => $tgl_presensi,
                        'jam_in' => $jam,
                        'kode_jadwal' => $kode_jadwal,
                        'kode_jam_kerja' => $jadwal->kode_jam_kerja,
                        'status' => 'h',
                    ];

                    $simpan = DB::table('presensi')->insert($data);
                    if ($simpan) {
                        echo "success|Terimkasih, Selamat Bekerja|in";
                    } else {
                        echo "error|Maaf Gagal absen, Hubungi Tim It|in";
                    }
                }
            }
        } else if ($status_scan == 1) {

            $ceklastpresensi = DB::table('presensi')
                ->join('jam_kerja', 'presensi.kode_jam_kerja', '=', 'jam_kerja.kode_jam_kerja')
                ->where('nik', $nik)->where('tgl_presensi', $lastday)->first();
            // $last_kode_jadwal = $ceklastpresensi->kode_jadwal;
            // $last_kode_jam_kerja = $ceklastpresensi->kode_jam_kerja;

            $last_lintashari = $ceklastpresensi != null  ? $ceklastpresensi->lintashari : "";
            $tgl_pulang_shift_3 = date("H:i", strtotime(($jam)));

            $jadwal_last = $this->getJamKerjaKaryawan($nik, $lastday);
            $kode_jam_kerja_last = $jadwal_last != null ? $jadwal_last->kode_jam_kerja : null;
            $kode_jadwal_last = $jadwal_last != null ? ($jadwal_last->kode_jadwal ?? null) : null;


            $kode_jam_kerja = $jadwal->kode_jam_kerja;

            if (!empty($last_lintashari)) {
                if ($jam_sekarang > "00:00" && $jam_sekarang <= "14:00") {
                    $tgl_presensi = $lastday;
                }

                if ($hariini != "Sabtu") {
                    $tgl_pulang = date('Y-m-d', strtotime('+1 day', strtotime($tgl_presensi)));
                    $jam_pulang = $tgl_pulang . " " . date("H:i", strtotime($ceklastpresensi->jam_pulang));
                } else {
                    $tgl_pulang = $tgl_presensi;
                    $jam_pulang = $tgl_pulang . " " . date("H:i", strtotime($jam_kerja->jam_pulang));
                }

                //echo "A" . $jam_pulang;
            } else {
                if ($tgl_pulang_shift_3 <= "14:00" && ($kode_jadwal_last == "JD004" || $kode_jadwal_last == "JD04" || $kode_jam_kerja_last == "JK04")) {
                    $tgl_presensi = $lastday;
                    $tgl_pulang = date('Y-m-d', strtotime('+1 day', strtotime($tgl_presensi)));
                    $jam_pulang = $tgl_pulang . " 07:00";
                    $kode_jam_kerja = "JK08";
                    $kode_jadwal = "JD004";
                    //echo 'B';
                } else {

                    if ($kode_jadwal == "JD004") {
                        if ($hariini != "Sabtu") {
                            if ($jam_sekarang > "00:00" && $jam_sekarang <= "14:00") {
                                $tgl_pulang = $tgl_presensi;
                            } else {
                                $tgl_pulang = date('Y-m-d', strtotime('+1 day', strtotime($tgl_presensi)));
                            }
                        } else {
                            $tgl_pulang = $tgl_presensi;
                        }
                    } else {
                        $tgl_pulang = $tgl_presensi;
                    }

                    //echo 'C';
                    $jam_pulang = $tgl_pulang . " " . date("H:i", strtotime($jam_kerja->jam_pulang));
                }
                // $tgl_pulang = $tgl_presensi;
                // $jam_pulang = $tgl_pulang . " " . $jam_kerja->jam_pulang;

            }

            $jam_pulang_formatted = date("Y-m-d H:i", strtotime($jam_pulang));
            $jamabsen = $jam;
            if (strtotime($jamabsen) < strtotime($jam_pulang)) {
                echo "error|Maaf Belum Waktunya Absen Pulang, Absen Pulang di Mulai Pada Pukul "  . " " . $jam_pulang_formatted . " |out";
            } else {
                $cek = DB::table('presensi')->where('tgl_presensi', $tgl_presensi)->where('nik', $nik)->first();
                if ($cek == null) {
                    $data = [
                        'nik' => $nik,
                        'tgl_presensi' => $tgl_presensi,
                        'jam_out' => $jam,
                        'kode_jadwal' => $kode_jadwal,
                        'kode_jam_kerja' => $kode_jam_kerja,
                        'status' => 'h',
                    ];

                    $simpan = DB::table('presensi')->insert($data);
                    if ($simpan) {
                        echo "success|Terimkasih, Hati Hati Di Jalan|out";
                    } else {
                        echo "error|Maaf Gagal absen, Hubungi Tim It|out";
                    }
                } else if ($cek != null && !empty($cek->jam_out)) {
                    echo "error|Maaf Gagal absen, Anda Sudah Melakukan Presensi Pulang|in";
                } else if ($cek != null && empty($cek->jam_out)) {
                    $data_masuk = [
                        'jam_out' => $jam
                    ];
                    $update = DB::table('presensi')->where('tgl_presensi', $tgl_presensi)->where('nik', $nik)->update($data_masuk);
                    if ($update) {
                        echo "success|Terimkasih, Hati Hati Di Jalan|out";
                    } else {
                        echo "error|Maaf Gagal absen, Hubungi Tim It|out";
                    }
                }
            }
        }
    }

    private function getJamKerjaKaryawan($nik, $tanggal, $hari_nama = null)
    {
        if ($hari_nama === null) {
            $hari_nama = $this->hari_tanggal($tanggal);
        }

        // 1. Cek By Date
        $cek_by_date = DB::table('konfigurasi_jk_karyawan_by_date')
            ->where('nik', $nik)
            ->where('tanggal', $tanggal)
            ->first();
        if ($cek_by_date != null) {
            return (object) [
                'nama_jadwal' => 'Jadwal Khusus Tanggal',
                'kode_cabang' => 'PST',
                'kode_jam_kerja' => $cek_by_date->kode_jam_kerja,
                'hari' => $hari_nama
            ];
        }

        // 2. Cek Weekly Shift Schedule
        $cekjadwalshift = DB::table('konfigurasi_jadwalkerja_detail')
            ->join('konfigurasi_jadwalkerja', 'konfigurasi_jadwalkerja_detail.kode_setjadwal', '=', 'konfigurasi_jadwalkerja.kode_setjadwal')
            ->whereRaw('"' . $tanggal . '" >= dari')
            ->whereRaw('"' . $tanggal . '" <= sampai')
            ->where('nik', $nik)
            ->first();
        if ($cekjadwalshift != null) {
            $jadwal = DB::table('jadwal_kerja_detail')
                ->join('jadwal_kerja', 'jadwal_kerja_detail.kode_jadwal', '=', 'jadwal_kerja.kode_jadwal')
                ->where('hari', $hari_nama)->where('jadwal_kerja_detail.kode_jadwal', $cekjadwalshift->kode_jadwal)->first();
            if ($jadwal) {
                return $jadwal;
            }
        }

        // 3. Cek Daily Config (Senin-Minggu)
        $cek_daily = DB::table('konfigurasi_jk_karyawan')
            ->where('nik', $nik)
            ->where('hari', $hari_nama)
            ->first();
        if ($cek_daily != null) {
            return (object) [
                'nama_jadwal' => 'Jadwal Hari Karyawan',
                'kode_cabang' => 'PST',
                'kode_jam_kerja' => $cek_daily->kode_jam_kerja,
                'hari' => $hari_nama
            ];
        }

        // 4. Default / Fallback
        $karyawan = DB::table('karyawan')->where('nik', $nik)->first();
        $kode_jadwal = $karyawan->kode_jadwal ?? 'JD01';
        $jadwal = DB::table('jadwal_kerja_detail')
            ->join('jadwal_kerja', 'jadwal_kerja_detail.kode_jadwal', '=', 'jadwal_kerja.kode_jadwal')
            ->where('hari', $hari_nama)->where('jadwal_kerja_detail.kode_jadwal', $kode_jadwal)->first();
        return $jadwal;
    }

    public function idcard()
    {
        $nik = Auth::guard('karyawan')->user()->nik;
        $karyawan = Karyawan::where('nik', $nik)
            ->leftJoin('cabang', 'karyawan.kode_cabang', '=', 'cabang.kode_cabang')
            ->first();

        if ($karyawan) {
            $karyawan->nama_jabatan = $karyawan->jabatan;
            $karyawan->nama_cabang = $karyawan->nama_cabang ?? 'Kantor Pusat';
        }

        return view('presensi.idcard', compact('karyawan'));
    }
}
