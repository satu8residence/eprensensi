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
        // 1. Alter karyawan table
        if (Schema::hasTable('karyawan')) {
            Schema::table('karyawan', function (Blueprint $table) {
                if (!Schema::hasColumn('karyawan', 'kode_cabang')) {
                    $table->char('kode_cabang', 10)->nullable();
                }
                if (!Schema::hasColumn('karyawan', 'lock_location')) {
                    $table->tinyInteger('lock_location')->default(0);
                }
                if (!Schema::hasColumn('karyawan', 'kode_jabatan')) {
                    $table->char('kode_jabatan', 10)->nullable();
                }
            });
            // Update existing karyawan to default cabang code 'TGSL'
            DB::table('karyawan')->update(['kode_cabang' => 'TGSL']);
        }

        // 2. Alter presensi table
        if (Schema::hasTable('presensi')) {
            Schema::table('presensi', function (Blueprint $table) {
                if (!Schema::hasColumn('presensi', 'status')) {
                    $table->char('status', 1)->default('h');
                }
                if (!Schema::hasColumn('presensi', 'kode_jadwal')) {
                    $table->char('kode_jadwal', 10)->nullable();
                }
                if (!Schema::hasColumn('presensi', 'kode_jam_kerja')) {
                    $table->char('kode_jam_kerja', 10)->nullable();
                }
            });
            // Update existing presensi to default values
            DB::table('presensi')->update([
                'status' => 'h',
                'kode_jadwal' => 'JD01',
                'kode_jam_kerja' => 'JK01'
            ]);
        }

        // 3. Rename and alter izin table to pengajuan_izin
        if (Schema::hasTable('izin') && !Schema::hasTable('pengajuan_izin')) {
            Schema::rename('izin', 'pengajuan_izin');
        }

        if (Schema::hasTable('pengajuan_izin')) {
            Schema::table('pengajuan_izin', function (Blueprint $table) {
                if (!Schema::hasColumn('pengajuan_izin', 'kode_izin')) {
                    $table->string('kode_izin', 20)->nullable();
                }
            });
        }

        // 4. Create jam_kerja table
        if (!Schema::hasTable('jam_kerja')) {
            Schema::create('jam_kerja', function (Blueprint $table) {
                $table->char('kode_jam_kerja', 10)->primary();
                $table->string('nama_jam_kerja', 50);
                $table->time('jam_masuk');
                $table->time('jam_pulang');
                $table->tinyInteger('lintashari')->default(0);
                $table->decimal('total_jam', 5, 2)->default(8.00);
                $table->tinyInteger('istirahat')->default(0);
                $table->time('jam_awal_istirahat')->nullable();
                $table->time('jam_akhir_istirahat')->nullable();
                $table->timestamps();
            });

            // Insert default jam kerja
            DB::table('jam_kerja')->insert([
                [
                    'kode_jam_kerja' => 'JK01',
                    'nama_jam_kerja' => 'Jam Kerja Standar',
                    'jam_masuk' => '08:00:00',
                    'jam_pulang' => '17:00:00',
                    'lintashari' => 0,
                    'total_jam' => 8.00,
                    'istirahat' => 1,
                    'jam_awal_istirahat' => '12:00:00',
                    'jam_akhir_istirahat' => '13:00:00',
                    'created_at' => now(),
                    'updated_at' => now()
                ]
            ]);
        }

        // 5. Create jadwal_kerja table
        if (!Schema::hasTable('jadwal_kerja')) {
            Schema::create('jadwal_kerja', function (Blueprint $table) {
                $table->char('kode_jadwal', 10)->primary();
                $table->string('nama_jadwal', 50);
                $table->string('kode_cabang', 10);
                $table->timestamps();
            });

            // Insert default jadwal kerja
            DB::table('jadwal_kerja')->insert([
                [
                    'kode_jadwal' => 'JD01',
                    'nama_jadwal' => 'Jadwal Standar Tangerang Selatan',
                    'kode_cabang' => 'TGSL',
                    'created_at' => now(),
                    'updated_at' => now()
                ]
            ]);
        }

        // 6. Create jadwal_kerja_detail table
        if (!Schema::hasTable('jadwal_kerja_detail')) {
            Schema::create('jadwal_kerja_detail', function (Blueprint $table) {
                $table->id();
                $table->char('kode_jadwal', 10);
                $table->string('hari', 10);
                $table->char('kode_jam_kerja', 10);
                $table->timestamps();

                $table->foreign('kode_jadwal')->references('kode_jadwal')->on('jadwal_kerja')->onDelete('cascade');
                $table->foreign('kode_jam_kerja')->references('kode_jam_kerja')->on('jam_kerja')->onDelete('cascade');
            });

            // Insert default detail for all days mapping to Standard Shift
            $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
            $details = [];
            foreach ($days as $day) {
                $details[] = [
                    'kode_jadwal' => 'JD01',
                    'hari' => $day,
                    'kode_jam_kerja' => 'JK01',
                    'created_at' => now(),
                    'updated_at' => now()
                ];
            }
            DB::table('jadwal_kerja_detail')->insert($details);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('jadwal_kerja_detail');
        Schema::dropIfExists('jadwal_kerja');
        Schema::dropIfExists('jam_kerja');

        if (Schema::hasTable('pengajuan_izin')) {
            Schema::table('pengajuan_izin', function (Blueprint $table) {
                if (Schema::hasColumn('pengajuan_izin', 'kode_izin')) {
                    $table->dropColumn('kode_izin');
                }
            });
            Schema::rename('pengajuan_izin', 'izin');
        }

        if (Schema::hasTable('presensi')) {
            Schema::table('presensi', function (Blueprint $table) {
                $table->dropColumn(['status', 'kode_jadwal', 'kode_jam_kerja']);
            });
        }

        if (Schema::hasTable('karyawan')) {
            Schema::table('karyawan', function (Blueprint $table) {
                $table->dropColumn(['kode_cabang', 'lock_location', 'kode_jabatan']);
            });
        }
    }
};
