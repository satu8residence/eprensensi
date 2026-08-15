<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PresensiController extends Controller
{

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

    public function store()
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
        $karyawan       = DB::table('karyawan')->where('pin', $pin)->first();
        $jabatan        = (object)['nama_jabatan' => $karyawan->jabatan, 'kategori' => ''];

        if ($karyawan == null) {
            echo "PIN Tidak Ditemukan";
            $nik = "";
        } else {
            $nik = $karyawan->nik;
        }
        $cekperjalanandinas = null;
        if ($cekperjalanandinas != null) {
            $kode_cabang = $cekperjalanandinas->kode_cabang;
        } else {
            $kode_cabang = $karyawan->kode_cabang;
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

        $libur = null;
        $tanggal_libur = $datalibur != null ? $datalibur->tanggal_libur : '';

        $ceklembur = null;


        $jam_kerja = DB::table('jam_kerja')->where('kode_jam_kerja', $jadwal->kode_jam_kerja)->first();
        $lintashari  = $jam_kerja->lintashari;

        $cek = DB::table('presensi')->where('tgl_presensi', $tgl_presensi)->where('nik', $nik)->first();

        $jam_sekarang = date("H:i:s");


        if (in_array($status_scan, [0, 2, 4, 6, 8])) {
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
        } else {

            $ceklastpresensi = DB::table('presensi')
                ->join('jam_kerja', 'presensi.kode_jam_kerja', '=', 'jam_kerja.kode_jam_kerja')
                ->where('nik', $nik)->where('tgl_presensi', $lastday)->first();
            // $last_kode_jadwal = $ceklastpresensi->kode_jadwal;
            // $last_kode_jam_kerja = $ceklastpresensi->kode_jam_kerja;

            $last_lintashari = $ceklastpresensi != null  ? $ceklastpresensi->lintashari : "";
            $tgl_pulang_shift_3 = date("H:i", strtotime(($jam)));

            $cekjadwalshiftlast = null;
            $kode_jadwal_last = $cekjadwalshiftlast != null ? $cekjadwalshiftlast->kode_jadwal : $kode_jadwal;


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
                if ($tgl_pulang_shift_3 <= "14:00" && $kode_jadwal_last == "JD004") {
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
}
