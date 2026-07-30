<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>A4</title>

    <!-- Normalize or reset CSS with your favorite library -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/7.0.0/normalize.min.css">

    <!-- Load paper.css for happy printing -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/paper-css/0.4.1/paper.css">

    <!-- Set page size here: A5, A4 or A3 -->
    <!-- Set also "landscape" if you need -->
    <style>
        @page {
            size: A4
        }

        #title {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 18px;
            font-weight: bold;
        }

        .tabeldatakaryawan {
            margin-top: 40px;
        }

        .tabeldatakaryawan tr td {
            padding: 5px;
        }

        .tabelpresensi {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
        }

        .tabelpresensi tr th {
            border: 1px solid #131212;
            padding: 8px;
            background-color: #dbdbdb;
        }

        .tabelpresensi tr td {
            border: 1px solid #131212;
            padding: 5px;
            font-size: 12px;
        }

        .foto {
            width: 40px;
            height: 30px;

        }

        @media print {
            .d-print-none {
                display: none !important;
            }
        }

    </style>
</head>

<!-- Set "A5", "A4" or "A3" for class name -->
<!-- Set also "landscape" if you need -->
<body class="A4">
    <div class="d-print-none" style="background: #206bc4; padding: 10px; text-align: center; color: white; font-family: sans-serif; position: sticky; top: 0; left: 0; width: 100%; box-shadow: 0 2px 4px rgba(0,0,0,0.1); z-index: 9999;">
        <button onclick="window.print()" style="background: #2fb344; color: white; border: none; padding: 8px 16px; border-radius: 4px; font-weight: bold; cursor: pointer; margin-right: 10px;">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: middle; margin-right: 5px;">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                <path d="M17 17h2a2 2 0 0 0 2 -2v-4a2 2 0 0 0 -2 -2h-14a2 2 0 0 0 -2 2v4a2 2 0 0 0 2 2h2"></path>
                <path d="M17 9v-4a2 2 0 0 0 -2 -2h-6a2 2 0 0 0 -2 2v4"></path>
                <path d="M7 13m0 2a2 2 0 0 1 2 -2h6a2 2 0 0 1 2 2v4a2 2 0 0 1 -2 2h-6a2 2 0 0 1 -2 -2z"></path>
            </svg>
            Cetak / Simpan PDF
        </button>
        <button onclick="window.close()" style="background: #d63939; color: white; border: none; padding: 8px 16px; border-radius: 4px; font-weight: bold; cursor: pointer;">
            Tutup Halaman
        </button>
    </div>
    <?php
    if (!function_exists('selisih')) {
        function selisih($jam_masuk, $jam_keluar)
        {
            list($h, $m, $s) = explode(":", $jam_masuk);
            $dtAwal = mktime($h, $m, $s, "1", "1", "1");
            list($h, $m, $s) = explode(":", $jam_keluar);
            $dtAkhir = mktime($h, $m, $s, "1", "1", "1");
            $dtSelisih = $dtAkhir - $dtAwal;
            $totalmenit = $dtSelisih / 60;
            $jam = explode(".", $totalmenit / 60);
            $sisamenit = ($totalmenit / 60) - $jam[0];
            $sisamenit2 = $sisamenit * 60;
            $jml_jam = $jam[0];
            return $jml_jam . ":" . round($sisamenit2);
        }
    }

    if (!function_exists('hitung_jam_lembur')) {
        function hitung_jam_lembur($tgl, $jam_in, $jam_out, $overtime_start_str, $overtime_end_str) {
            if (empty($jam_in) || empty($jam_out)) {
                return 0;
            }
            
            $presence_start = strtotime($tgl . ' ' . $jam_in);
            $presence_end = strtotime($tgl . ' ' . $jam_out);
            if ($presence_end < $presence_start) {
                $presence_end += 86400; // Crossover day
            }
            
            $ot_start = strtotime($overtime_start_str);
            $ot_end = strtotime($overtime_end_str);
            if ($ot_end < $ot_start) {
                $ot_end += 86400;
            }
            
            $intersect_start = max($presence_start, $ot_start);
            $intersect_end = min($presence_end, $ot_end);
            
            if ($intersect_start < $intersect_end) {
                return ($intersect_end - $intersect_start) / 3600;
            }
            
            return 0;
        }
    }

    if (!function_exists('format_hours_to_time')) {
        function format_hours_to_time($hours) {
            if ($hours <= 0) {
                return "00:00";
            }
            $h = floor($hours);
            $m = round(($hours - $h) * 60);
            if ($m == 60) {
                $h += 1;
                $m = 0;
            }
            return sprintf("%02d:%02d", $h, $m);
        }
    }
    ?>
    <!-- Each sheet element should have the class "sheet" -->
    <!-- "padding-**mm" is optional: you can set 10, 15, 20 or 25 -->
    <section class="sheet padding-10mm">

        <table style="width: 100%">
            <tr>
                <td style="width: 30px">
                    <img src="{{ asset('assets/img/logopresensi.png') }}" width="70" height="70" alt="">
                </td>
                <td>
                    <span id="title">
                        LAPORAN PRESENSI KARYAWAN<br>
                        PERIODE {{ strtoupper($namabulan[$bulan]) }} {{ $tahun }}<br>
                        SATU8 RESIDENCE<br>
                    </span>
                    <span><i>Jalan Pilar Kompleks Delta Kedoya Kavling 18 Blok S, Kedoya Selatan, Kebon Jeruk, Jakarta Barat, 11520</i></span>
                </td>
            </tr>
        </table>
        <table class="tabeldatakaryawan">
            <tr>
                <td rowspan="6">
                    @php
                    $path = Storage::url('uploads/karyawan/'.$karyawan->foto);
                    @endphp
                    <img src="{{ url($path) }}" alt="" width="120px" height="150">
                </td>
            </tr>
            <tr>
                <td>NIK</td>
                <td>:</td>
                <td>{{ $karyawan->nik }}</td>
            </tr>
            <tr>
                <td>Nama Karyawan</td>
                <td>:</td>
                <td>{{ $karyawan->nama_lengkap }}</td>
            </tr>
            <tr>
                <td>Jabatan</td>
                <td>:</td>
                <td>{{ $karyawan->jabatan }}</td>
            </tr>
            <tr>
                <td>Departemen</td>
                <td>:</td>
                <td>{{ $karyawan->nama_dept }}</td>
            </tr>
            <tr>
                <td>No. HP</td>
                <td>:</td>
                <td>{{ $karyawan->no_hp }}</td>
            </tr>
        </table>
        <table class="tabelpresensi">
            <tr>
                <th>No.</th>
                <th>Tanggal</th>
                <th>Jam Masuk</th>
                <th>Foto</th>
                <th>Jam Pulang</th>
                <th>Foto</th>
                <th>Keterangan</th>
                <th>Jml Jam</th>
                <th>Lembur</th>
            </tr>
            @php
                $total_lembur_hours = 0;
                $total_kerja_hours = 0;
            @endphp
            @foreach ($presensi as $d)
            @php
            $path_in = Storage::url('uploads/absensi/'.$d->foto_in);
            $path_out = Storage::url('uploads/absensi/'.$d->foto_out);
            $jamterlambat = selisih('07:00:00',$d->jam_in);

            $l = $lembur->get($d->tgl_presensi);
            
            $jmljamkerja_str = '00:00';
            $jam_lembur_str = '-';
            
            if ($d->jam_out != null) {
                $t_in = strtotime($d->tgl_presensi . ' ' . $d->jam_in);
                $t_out = strtotime($d->tgl_presensi . ' ' . $d->jam_out);
                if ($t_out < $t_in) {
                    $t_out += 86400;
                }
                $total_presence_hours = ($t_out - $t_in) / 3600;
                
                $ot_hours = 0;
                if ($l) {
                    $ot_hours = hitung_jam_lembur($d->tgl_presensi, $d->jam_in, $d->jam_out, $l->tanggal_dari, $l->tanggal_sampai);
                }
                
                $jam_kerja_hours = max(0, $total_presence_hours - $ot_hours);
                
                $jmljamkerja_str = format_hours_to_time($jam_kerja_hours);
                $jam_lembur_str = $ot_hours > 0 ? format_hours_to_time($ot_hours) : '-';
                
                $total_lembur_hours += $ot_hours;
                $total_kerja_hours += $jam_kerja_hours;
            }
            @endphp
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ date("d-m-Y",strtotime($d->tgl_presensi)) }}</td>
                <td>{{ $d->jam_in }}</td>
                <td><img src="{{ url($path_in) }}" alt="" class="foto"></td>
                <td>{{ $d->jam_out != null ? $d->jam_out : 'Belum Absen' }}</td>
                <td>
                    @if ($d->jam_out != null)
                    <img src="{{ url($path_out) }}" alt="" class="foto">
                    @else
                    <img src="{{ asset('assets/img/camera.jpg') }}" alt="" class="foto">
                    @endif
                </td>
                <td>
                    @if ($d->jam_in > '07:00')
                    Terlambat {{ $jamterlambat }}
                    @else
                    Tepat Waktu
                    @endif
                </td>
                <td>{{ $jmljamkerja_str }}</td>
                <td>{{ $jam_lembur_str }}</td>
            </tr>
            @endforeach
            <tr>
                <td colspan="7" style="text-align: right; font-weight: bold;">Total Jam</td>
                <td style="font-weight: bold;">{{ format_hours_to_time($total_kerja_hours) }}</td>
                <td style="font-weight: bold;">{{ format_hours_to_time($total_lembur_hours) }}</td>
            </tr>
        </table>

        @php
            $config_laporan = DB::table('konfigurasi_lokasi')->where('id', 1)->first();
            $nama_hrd = $config_laporan != null ? $config_laporan->nama_hrd : 'Qiana Aqila';
            $jabatan_hrd = $config_laporan != null ? $config_laporan->jabatan_hrd : 'HRD Manager';
            $nama_pimpinan = $config_laporan != null ? $config_laporan->nama_pimpinan : 'Daffa';
            $jabatan_pimpinan = $config_laporan != null ? $config_laporan->jabatan_pimpinan : 'Direktur';
        @endphp
        <table width="100%" style="margin-top:100px">
            <tr>
                <td colspan="2" style="text-align: right">Jakarta, {{ date('d-m-Y') }}</td>
            </tr>
            <tr>
                <td style="text-align: center; vertical-align:bottom" height="100px">
                    <u>{{ $nama_hrd }}</u><br>
                    <i><b>{{ $jabatan_hrd }}</b></i>
                </td>
                <td style="text-align: center; vertical-align:bottom">
                    <u>{{ $nama_pimpinan }}</u><br>
                    <i><b>{{ $jabatan_pimpinan }}</b></i>
                </td>
            </tr>
        </table>
    </section>

</body>

</html>
