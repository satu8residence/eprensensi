<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;

class SetjadwalController extends Controller
{
    public function index()
    {
        $jadwal_kerja = DB::table('jadwal_kerja')->orderBy('kode_jadwal')->get();
        $setjadwal = DB::table('konfigurasi_jadwalkerja')
            ->orderBy('dari', 'desc')
            ->paginate(10);

        return view('konfigurasi.setjadwal', compact('jadwal_kerja', 'setjadwal'));
    }

    public function downloadTemplate()
    {
        $karyawan = DB::table('karyawan')->orderBy('nama_lengkap')->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="template_jadwal_shift.csv"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];

        $callback = function() use ($karyawan) {
            $file = fopen('php://output', 'w');
            
            // Excel-friendly CSV using semicolon delimiter
            fputcsv($file, ['nik', 'nama_lengkap', 'kode_jadwal (JD01/JD02/JD03/JD04)'], ';');

            foreach ($karyawan as $k) {
                fputcsv($file, [$k->nik, $k->nama_lengkap, ''], ';');
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    public function store(Request $request)
    {
        $request->validate([
            'dari' => 'required|date',
            'sampai' => 'required|date',
            'file_csv' => 'required|file|mimes:csv,txt'
        ]);

        $dari = $request->dari;
        $sampai = $request->sampai;

        // Generate dynamic unique kode_setjadwal (max 10 chars)
        $kode_setjadwal = 'SJ' . date('md', strtotime($dari)) . rand(10, 99);

        // Check if there is an overlapping date range already
        $cek_overlap = DB::table('konfigurasi_jadwalkerja')
            ->where(function($query) use ($dari, $sampai) {
                $query->whereBetween('dari', [$dari, $sampai])
                      ->orWhereBetween('sampai', [$dari, $sampai]);
            })->first();

        if ($cek_overlap) {
            return Redirect::back()->with(['warning' => 'Tanggal tersebut sudah dikonfigurasi sebelumnya pada kode: ' . $cek_overlap->kode_setjadwal]);
        }

        try {
            DB::beginTransaction();

            DB::table('konfigurasi_jadwalkerja')->insert([
                'kode_setjadwal' => $kode_setjadwal,
                'dari' => $dari,
                'sampai' => $sampai,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            $file = $request->file('file_csv');
            $path = $file->getRealPath();
            $handle = fopen($path, 'r');

            // Autodetect delimiter (semicolon or comma)
            $firstLine = fgets($handle);
            $delimiter = ';';
            if (strpos($firstLine, ',') !== false && strpos($firstLine, ';') === false) {
                $delimiter = ',';
            }
            rewind($handle);

            // Skip header
            fgetcsv($handle, 0, $delimiter);

            while (($row = fgetcsv($handle, 0, $delimiter)) !== FALSE) {
                if (empty($row[0])) continue;

                $nik = trim($row[0]);
                $kode_jadwal = isset($row[2]) ? trim($row[2]) : '';

                if (!empty($nik) && !empty($kode_jadwal)) {
                    // Verify if NIK exists
                    $cek_karyawan = DB::table('karyawan')->where('nik', $nik)->first();
                    // Verify if kode_jadwal exists
                    $cek_jadwal = DB::table('jadwal_kerja')->where('kode_jadwal', $kode_jadwal)->first();

                    if ($cek_karyawan && $cek_jadwal) {
                        DB::table('konfigurasi_jadwalkerja_detail')->insert([
                            'kode_setjadwal' => $kode_setjadwal,
                            'nik' => $nik,
                            'kode_jadwal' => $kode_jadwal,
                            'created_at' => now(),
                            'updated_at' => now()
                        ]);
                    }
                }
            }

            fclose($handle);
            DB::commit();

            return Redirect::back()->with(['success' => 'Jadwal Shift Berhasil Diupload']);
        } catch (\Exception $e) {
            DB::rollBack();
            return Redirect::back()->with(['warning' => 'Gagal mengupload jadwal shift: ' . $e->getMessage()]);
        }
    }

    public function delete($kode_setjadwal)
    {
        $hapus = DB::table('konfigurasi_jadwalkerja')->where('kode_setjadwal', $kode_setjadwal)->delete();
        if ($hapus) {
            return Redirect::back()->with(['success' => 'Konfigurasi Jadwal Berhasil Dihapus']);
        } else {
            return Redirect::back()->with(['warning' => 'Konfigurasi Jadwal Gagal Dihapus']);
        }
    }
}
