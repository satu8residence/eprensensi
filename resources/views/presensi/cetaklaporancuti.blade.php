<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Laporan Cuti Per Tahun</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/7.0.0/normalize.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/paper-css/0.4.1/paper.css">
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
            font-size: 12px;
        }

        .tabelpresensi tr td {
            border: 1px solid #131212;
            padding: 5px;
            font-size: 12px;
        }

        @media print {
            .d-print-none {
                display: none !important;
            }
        }
    </style>
</head>

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
    
    <section class="sheet padding-10mm">
        <table style="width: 100%">
            <tr>
                <td style="width: 30px">
                    <img src="{{ asset('assets/img/logopresensi.png') }}" width="70" height="70" alt="">
                </td>
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
                <td>{{ $d->nik }}</td>
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
                <td style="text-align: right; padding-right: 50px;">Jakarta, {{ date('d-m-Y') }}</td>
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
