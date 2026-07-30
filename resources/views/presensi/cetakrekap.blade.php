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
            font-size: 10px
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
<body class="A4 landscape">
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
                        REKAP PRESENSI KARYAWAN<br>
                        PERIODE {{ strtoupper($namabulan[$bulan]) }} {{ $tahun }}<br>
                        SATU8 RESIDENCE<br>
                    </span>
                    <span><i>Jalan Pilar Kompleks Delta Kedoya Kavling 18 Blok S, Kedoya Selatan, Kebon Jeruk, Jakarta Barat, 11520</i></span>
                </td>
            </tr>
        </table>
        <table class="tabelpresensi">
            <tr>
                <th rowspan="2">Nik</th>
                <th rowspan="2">Nama Karyawan</th>
                <th colspan="31">Tanggal</th>
                <th rowspan="2">TH</th>
                <th rowspan="2">TT</th>
                <th rowspan="2">TL</th>
            </tr>
            <tr>
                <?php
                for($i=1; $i<=31; $i++){
                ?>
                <th>{{ $i }}</th>
                <?php
                }
                ?>

            </tr>
            @foreach ($rekap as $d)
            <tr>
                <td style="mso-number-format:'\@';">{{ $d->nik }}</td>
                <td>{{ $d->nama_lengkap }}</td>

                <?php
                $totalhadir = 0;
                $totalterlambat = 0;
                for($i=1; $i<=31; $i++){
                    $tgl = "tgl_".$i;
                    if(empty($d->$tgl)){
                        $hadir = ['',''];
                        $totalhadir += 0;
                    }else{
                        $hadir = explode("-",$d->$tgl);
                        $totalhadir += 1;
                        if($hadir[0] > "07:00:00"){
                            $totalterlambat +=1;
                        }
                    }
                ?>

                <td>
                    <span style="color:{{ $hadir[0]>"07:00:00" ? "red" : "" }}">{{ $hadir[0] }}</span><br>
                    <span style="color:{{ $hadir[1]<"16:00:00" ? "red" : "" }}">{{ $hadir[1] }}</span>
                </td>

                <?php
                }
                ?>
                <td>{{ $totalhadir }}</td>
                <td>{{ $totalterlambat }}</td>
                <td>{{ isset($total_lembur[$d->nik]) ? format_hours_to_time($total_lembur[$d->nik]) : '-' }}</td>
            </tr>
            @endforeach
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
                <td></td>
                <td style="text-align: center">Jakarta, {{ date('d-m-Y') }}</td>
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
