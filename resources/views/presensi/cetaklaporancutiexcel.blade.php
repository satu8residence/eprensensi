<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Laporan Cuti Per Tahun Excel</title>
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
    <table style="width: 100%">
        <tr>
            <td>
                <span id="title">
                    LAPORAN CUTI KARYAWAN<br>
                    PER TAHUN {{ $tahun }}<br>
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
                <th>Tgl Pengajuan</th>
                @if(!$karyawan_selected)
                <th>NIK</th>
                <th>Nama Karyawan</th>
                @endif
                <th>Jenis Cuti</th>
                <th>Periode Cuti</th>
                <th>Jml Hari</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @php
                $total_cuti_days = 0;
            @endphp
            @foreach ($cuti as $d)
            @php
                $total_cuti_days += $d->jmlhari;
            @endphp
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ date("d-m-Y", strtotime($d->tgl_izin)) }}</td>
                @if(!$karyawan_selected)
                <td style="mso-number-format:'\@';">{{ $d->nik }}</td>
                <td>{{ $d->nama_lengkap }}</td>
                @endif
                <td>{{ $d->nama_cuti }}</td>
                <td>
                    {{ date("d-m-Y", strtotime($d->dari)) }} s.d {{ date("d-m-Y", strtotime($d->sampai)) }}
                </td>
                <td style="text-align: center;">{{ $d->jmlhari }} Hari</td>
                <td>{{ $d->keterangan }}</td>
            </tr>
            @endforeach
            <tr>
                <td colspan="{{ $karyawan_selected ? 4 : 6 }}" style="text-align: right; font-weight: bold;">Total Hari Cuti</td>
                <td style="font-weight: bold; text-align: center;">{{ $total_cuti_days }} Hari</td>
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
