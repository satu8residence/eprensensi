<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Add missing columns to jam_kerja if not exists
        Schema::table('jam_kerja', function (Blueprint $table) {
            if (!Schema::hasColumn('jam_kerja', 'awal_jam_masuk')) {
                $table->time('awal_jam_masuk')->nullable()->after('jam_masuk');
            }
            if (!Schema::hasColumn('jam_kerja', 'akhir_jam_masuk')) {
                $table->time('akhir_jam_masuk')->nullable()->after('jam_pulang');
            }
        });

        // Create konfigurasi_jadwalkerja
        Schema::create('konfigurasi_jadwalkerja', function (Blueprint $table) {
            $table->string('kode_setjadwal', 10)->primary();
            $table->date('dari');
            $table->date('sampai');
            $table->timestamps();
        });

        // Create konfigurasi_jadwalkerja_detail
        Schema::create('konfigurasi_jadwalkerja_detail', function (Blueprint $table) {
            $table->id();
            $table->string('kode_setjadwal', 10);
            $table->string('nik', 12);
            $table->char('kode_jadwal', 10);
            $table->timestamps();

            $table->foreign('kode_setjadwal')->references('kode_setjadwal')->on('konfigurasi_jadwalkerja')->onDelete('cascade');
        });

        // Seed new shifts into jam_kerja if they don't exist
        $shifts = [
            [
                'kode_jam_kerja' => 'JK02',
                'nama_jam_kerja' => 'Shift 1',
                'jam_masuk' => '07:00:00',
                'jam_pulang' => '15:00:00',
                'awal_jam_masuk' => '06:00:00',
                'akhir_jam_masuk' => '09:00:00',
                'lintashari' => 0,
                'total_jam' => 8.00,
                'istirahat' => 1,
                'jam_awal_istirahat' => '12:00:00',
                'jam_akhir_istirahat' => '13:00:00',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'kode_jam_kerja' => 'JK03',
                'nama_jam_kerja' => 'Shift 2',
                'jam_masuk' => '15:00:00',
                'jam_pulang' => '23:00:00',
                'awal_jam_masuk' => '14:00:00',
                'akhir_jam_masuk' => '17:00:00',
                'lintashari' => 0,
                'total_jam' => 8.00,
                'istirahat' => 1,
                'jam_awal_istirahat' => '18:00:00',
                'jam_akhir_istirahat' => '19:00:00',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'kode_jam_kerja' => 'JK04',
                'nama_jam_kerja' => 'Shift 3 (Malam)',
                'jam_masuk' => '23:00:00',
                'jam_pulang' => '07:00:00',
                'awal_jam_masuk' => '22:00:00',
                'akhir_jam_masuk' => '01:00:00',
                'lintashari' => 1,
                'total_jam' => 8.00,
                'istirahat' => 1,
                'jam_awal_istirahat' => '02:00:00',
                'jam_akhir_istirahat' => '03:00:00',
                'created_at' => now(),
                'updated_at' => now()
            ]
        ];

        foreach ($shifts as $shift) {
            DB::table('jam_kerja')->updateOrInsert(
                ['kode_jam_kerja' => $shift['kode_jam_kerja']],
                $shift
            );
        }

        // Seed new schedule templates into jadwal_kerja & jadwal_kerja_detail
        $schedules = [
            [
                'kode_jadwal' => 'JD02',
                'nama_jadwal' => 'Jadwal Shift 1',
                'kode_cabang' => 'TGSL',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'kode_jadwal' => 'JD03',
                'nama_jadwal' => 'Jadwal Shift 2',
                'kode_cabang' => 'TGSL',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'kode_jadwal' => 'JD04',
                'nama_jadwal' => 'Jadwal Shift 3',
                'kode_cabang' => 'TGSL',
                'created_at' => now(),
                'updated_at' => now()
            ]
        ];

        foreach ($schedules as $schedule) {
            DB::table('jadwal_kerja')->updateOrInsert(
                ['kode_jadwal' => $schedule['kode_jadwal']],
                $schedule
            );
        }

        $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];

        // Detail for JD02 (Shift 1)
        foreach ($days as $day) {
            DB::table('jadwal_kerja_detail')->updateOrInsert(
                ['kode_jadwal' => 'JD02', 'hari' => $day],
                [
                    'kode_jam_kerja' => 'JK02',
                    'created_at' => now(),
                    'updated_at' => now()
                ]
            );
        }

        // Detail for JD03 (Shift 2)
        foreach ($days as $day) {
            DB::table('jadwal_kerja_detail')->updateOrInsert(
                ['kode_jadwal' => 'JD03', 'hari' => $day],
                [
                    'kode_jam_kerja' => 'JK03',
                    'created_at' => now(),
                    'updated_at' => now()
                ]
            );
        }

        // Detail for JD04 (Shift 3)
        foreach ($days as $day) {
            DB::table('jadwal_kerja_detail')->updateOrInsert(
                ['kode_jadwal' => 'JD04', 'hari' => $day],
                [
                    'kode_jam_kerja' => 'JK04',
                    'created_at' => now(),
                    'updated_at' => now()
                ]
            );
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('konfigurasi_jadwalkerja_detail');
        Schema::dropIfExists('konfigurasi_jadwalkerja');
    }
};
