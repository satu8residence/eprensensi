<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Laporan Lembur Excel</title>
    <style>
        #title {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 18px;
            font-weight: bold;
        }

        .tabeldatakaryawan {
            margin-top: 20px;
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
            font-size: 12px;
        }

        .tabelpresensi tr td {
            border: 1px solid #131212;
            padding: 5px;
            font-size: 12px;
        }
    </style>
</head>

<body>
    @php
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
    @endphp

    <table style="width: 100%">
        <tr>
            <td>
                <span id="title">
                    LAPORAN LEMBUR KARYAWAN<br>
                    PERIODE {{ strtoupper($namabulan[$bulan]) }} {{ $tahun }}<br>
                    SATU8 RESIDENCE<br>
                </span>
                <span><i>Jalan Pilar Kompleks Delta Kedoya Kavling 18 Blok S, Kedoya Selatan, Kebon Jeruk, Jakarta Barat, 11520</i></span>
            </td>
        </tr>
    </table>

    @if($karyawan_selected)
    <table class="tabeldatakaryawan">
        <tr>
            <td>NIK</td>
            <td>:</td>
            <td>{{ $karyawan_selected->nik }}</td>
        </tr>
        <tr>
            <td>Nama Karyawan</td>
            <td>:</td>
            <td>{{ $karyawan_selected->nama_lengkap }}</td>
        </tr>
        <tr>
            <td>Jabatan</td>
            <td>:</td>
            <td>{{ $karyawan_selected->jabatan }}</td>
        </tr>
        <tr>
            <td>Departemen</td>
            <td>:</td>
            <td>{{ $karyawan_selected->nama_dept }}</td>
        </tr>
        <tr>
            <td>No. HP</td>
            <td>:</td>
            <td>{{ $karyawan_selected->no_hp }}</td>
        </tr>
    </table>
    @endif

    <table class="tabelpresensi">
        <thead>
            <tr>
                <th>No.</th>
                <th>Tanggal</th>
                @if(!$karyawan_selected)
                <th>NIK</th>
                <th>Nama Karyawan</th>
                @endif
                <th>Jadwal Lembur</th>
                <th>Kehadiran Aktual</th>
                <th>Durasi Lembur</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @php
                $total_lembur_hours = 0;
            @endphp
            @foreach ($lembur as $d)
            @php
                $jam_lembur_str = '00:00';
                $ot_hours = 0;
                
                if ($d->jam_in && $d->jam_out) {
                    $ot_hours = hitung_jam_lembur($d->tanggal, $d->jam_in, $d->jam_out, $d->tanggal_dari, $d->tanggal_sampai);
                    $jam_lembur_str = format_hours_to_time($ot_hours);
                    $total_lembur_hours += $ot_hours;
                }
            @endphp
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ date("d-m-Y", strtotime($d->tanggal)) }}</td>
                @if(!$karyawan_selected)
                <td style="mso-number-format:'\@';">{{ $d->nik }}</td>
                <td>{{ $d->nama_lengkap }}</td>
                @endif
                <td>
                    {{ date("H:i", strtotime($d->tanggal_dari)) }} - {{ date("H:i", strtotime($d->tanggal_sampai)) }}
                </td>
                <td>
                    {{ $d->jam_in ? date("H:i", strtotime($d->jam_in)) : 'Belum Absen' }} - {{ $d->jam_out ? date("H:i", strtotime($d->jam_out)) : 'Belum Absen' }}
                </td>
                <td>{{ $jam_lembur_str }}</td>
                <td>{{ $d->keterangan }}</td>
            </tr>
            @endforeach
            <tr>
                <td colspan="{{ $karyawan_selected ? 4 : 6 }}" style="text-align: right; font-weight: bold;">Total Lembur</td>
                <td style="font-weight: bold;">{{ format_hours_to_time($total_lembur_hours) }}</td>
                <td></td>
            </tr>
        </tbody>
    </table>

    @php
        $config_laporan = DB::table('konfigurasi_lokasi')->where('id', 1)->first();
        $nama_hrd = $config_laporan != null ? $config_laporan->nama_hrd : 'Qiana Aqila';
        $jabatan_hrd = $config_laporan != null ? $config_laporan->jabatan_hrd : 'HRD Manager';
        $nama_pimpinan = $config_laporan != null ? $config_laporan->nama_pimpinan : 'Daffa';
        $jabatan_pimpinan = $config_laporan != null ? $config_laporan->jabatan_pimpinan : 'Direktur';
    @endphp
    <table width="100%" style="margin-top:50px">
        <tr>
            <td></td>
            <td style="text-align: right;">Jakarta, {{ date('d-m-Y') }}</td>
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
</body>

</html>
