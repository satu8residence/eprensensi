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
        // 1. Create hrd_jeniscuti table
        if (!Schema::hasTable('hrd_jeniscuti')) {
            Schema::create('hrd_jeniscuti', function (Blueprint $table) {
                $table->char('kode_cuti', 10)->primary();
                $table->string('nama_cuti', 50);
                $table->integer('jml_hari')->default(0);
                $table->timestamps();
            });

            // Seed default cuti types
            DB::table('hrd_jeniscuti')->insert([
                ['kode_cuti' => 'C01', 'nama_cuti' => 'Cuti Tahunan', 'jml_hari' => 12, 'created_at' => now(), 'updated_at' => now()],
                ['kode_cuti' => 'C02', 'nama_cuti' => 'Cuti Hamil/Melahirkan', 'jml_hari' => 90, 'created_at' => now(), 'updated_at' => now()],
                ['kode_cuti' => 'C03', 'nama_cuti' => 'Cuti Khusus', 'jml_hari' => 0, 'created_at' => now(), 'updated_at' => now()]
            ]);
        }

        // 2. Create hrd_jeniscuti_khusus table
        if (!Schema::hasTable('hrd_jeniscuti_khusus')) {
            Schema::create('hrd_jeniscuti_khusus', function (Blueprint $table) {
                $table->char('kode_cuti_khusus', 10)->primary();
                $table->string('nama_cuti_khusus', 50);
                $table->integer('jml_hari')->default(0);
                $table->timestamps();
            });

            // Seed default cuti khusus types
            DB::table('hrd_jeniscuti_khusus')->insert([
                ['kode_cuti_khusus' => 'CK01', 'nama_cuti_khusus' => 'Cuti Menikah', 'jml_hari' => 3, 'created_at' => now(), 'updated_at' => now()],
                ['kode_cuti_khusus' => 'CK02', 'nama_cuti_khusus' => 'Cuti Khitanan/Baptis Anak', 'jml_hari' => 2, 'created_at' => now(), 'updated_at' => now()],
                ['kode_cuti_khusus' => 'CK03', 'nama_cuti_khusus' => 'Cuti Anggota Keluarga Meninggal', 'jml_hari' => 3, 'created_at' => now(), 'updated_at' => now()],
                ['kode_cuti_khusus' => 'CK04', 'nama_cuti_khusus' => 'Cuti Istri Melahirkan', 'jml_hari' => 2, 'created_at' => now(), 'updated_at' => now()]
            ]);
        }

        // 3. Create hrd_izinabsen table
        if (!Schema::hasTable('hrd_izinabsen')) {
            Schema::create('hrd_izinabsen', function (Blueprint $table) {
                $table->string('kode_izin', 20)->primary();
                $table->char('nik', 15);
                $table->char('kode_jabatan', 10)->nullable();
                $table->char('kode_dept', 3)->nullable();
                $table->char('kode_cabang', 10)->nullable();
                $table->date('tanggal');
                $table->date('dari');
                $table->date('sampai');
                $table->text('keterangan');
                $table->tinyInteger('status')->default(0);
                $table->tinyInteger('direktur')->default(0);
                $table->integer('id_user')->default(1);
                $table->timestamps();
            });
        }

        // 4. Create hrd_izinsakit table
        if (!Schema::hasTable('hrd_izinsakit')) {
            Schema::create('hrd_izinsakit', function (Blueprint $table) {
                $table->string('kode_izin_sakit', 20)->primary();
                $table->char('nik', 15);
                $table->char('kode_jabatan', 10)->nullable();
                $table->char('kode_dept', 3)->nullable();
                $table->char('kode_cabang', 10)->nullable();
                $table->date('tanggal');
                $table->date('dari');
                $table->date('sampai');
                $table->text('keterangan');
                $table->tinyInteger('status')->default(0);
                $table->tinyInteger('direktur')->default(0);
                $table->integer('id_user')->default(1);
                $table->string('doc_sid')->nullable();
                $table->timestamps();
            });
        }

        // 5. Create hrd_izincuti table
        if (!Schema::hasTable('hrd_izincuti')) {
            Schema::create('hrd_izincuti', function (Blueprint $table) {
                $table->string('kode_izin_cuti', 20)->primary();
                $table->char('nik', 15);
                $table->char('kode_jabatan', 10)->nullable();
                $table->char('kode_dept', 3)->nullable();
                $table->char('kode_cabang', 10)->nullable();
                $table->date('tanggal');
                $table->date('dari');
                $table->date('sampai');
                $table->char('kode_cuti', 10);
                $table->char('kode_cuti_khusus', 10)->nullable();
                $table->text('keterangan');
                $table->tinyInteger('status')->default(0);
                $table->tinyInteger('direktur')->default(0);
                $table->integer('id_user')->default(1);
                $table->string('doc_cuti')->nullable();
                $table->timestamps();
            });
        }

        // 6. Create hrd_izinkeluar table
        if (!Schema::hasTable('hrd_izinkeluar')) {
            Schema::create('hrd_izinkeluar', function (Blueprint $table) {
                $table->string('kode_izin_keluar', 20)->primary();
                $table->char('nik', 15);
                $table->char('kode_jabatan', 10)->nullable();
                $table->char('kode_dept', 3)->nullable();
                $table->char('kode_cabang', 10)->nullable();
                $table->date('tanggal');
                $table->time('jam_keluar');
                $table->time('jam_kembali')->nullable();
                $table->text('keterangan');
                $table->tinyInteger('status')->default(0);
                $table->tinyInteger('direktur')->default(0);
                $table->integer('id_user')->default(1);
                $table->timestamps();
            });
        }

        // 7. Create hrd_izinpulang table
        if (!Schema::hasTable('hrd_izinpulang')) {
            Schema::create('hrd_izinpulang', function (Blueprint $table) {
                $table->string('kode_izin_pulang', 20)->primary();
                $table->char('nik', 15);
                $table->char('kode_jabatan', 10)->nullable();
                $table->char('kode_dept', 3)->nullable();
                $table->char('kode_cabang', 10)->nullable();
                $table->date('tanggal');
                $table->time('jam_pulang');
                $table->text('keterangan');
                $table->tinyInteger('status_approved')->default(0);
                $table->tinyInteger('direktur')->default(0);
                $table->integer('id_user')->default(1);
                $table->timestamps();
            });
        }

        // 8. Create hrd_izinterlambat table
        if (!Schema::hasTable('hrd_izinterlambat')) {
            Schema::create('hrd_izinterlambat', function (Blueprint $table) {
                $table->string('kode_izin_terlambat', 20)->primary();
                $table->char('nik', 15);
                $table->char('kode_jabatan', 10)->nullable();
                $table->char('kode_dept', 3)->nullable();
                $table->char('kode_cabang', 10)->nullable();
                $table->date('tanggal');
                $table->time('jam_terlambat');
                $table->text('keterangan');
                $table->tinyInteger('status')->default(0);
                $table->tinyInteger('direktur')->default(0);
                $table->integer('id_user')->default(1);
                $table->timestamps();
            });
        }

        // 9. Create hrd_izindinas table
        if (!Schema::hasTable('hrd_izindinas')) {
            Schema::create('hrd_izindinas', function (Blueprint $table) {
                $table->string('kode_izin_dinas', 20)->primary();
                $table->char('nik', 15);
                $table->char('kode_jabatan', 10)->nullable();
                $table->char('kode_dept', 3)->nullable();
                $table->char('kode_cabang', 10)->nullable();
                $table->date('tanggal');
                $table->date('dari');
                $table->date('sampai');
                $table->text('keterangan');
                $table->tinyInteger('status')->default(0);
                $table->tinyInteger('direktur')->default(0);
                $table->integer('id_user')->default(1);
                $table->timestamps();
            });
        }

        // 10. Create disposisi tables
        if (!Schema::hasTable('hrd_izinabsen_disposisi')) {
            Schema::create('hrd_izinabsen_disposisi', function (Blueprint $table) {
                $table->string('kode_disposisi', 20)->primary();
                $table->string('kode_izin', 20);
                $table->integer('id_pengirim');
                $table->integer('id_penerima');
                $table->tinyInteger('status')->default(0);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('hrd_izinsakit_disposisi')) {
            Schema::create('hrd_izinsakit_disposisi', function (Blueprint $table) {
                $table->string('kode_disposisi', 20)->primary();
                $table->string('kode_izin_sakit', 20);
                $table->integer('id_pengirim');
                $table->integer('id_penerima');
                $table->tinyInteger('status')->default(0);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('hrd_izincuti_disposisi')) {
            Schema::create('hrd_izincuti_disposisi', function (Blueprint $table) {
                $table->string('kode_disposisi', 20)->primary();
                $table->string('kode_izin_cuti', 20);
                $table->integer('id_pengirim');
                $table->integer('id_penerima');
                $table->tinyInteger('status')->default(0);
                $table->timestamps();
            });
        }

        // 11. Create presensi relation tables
        if (!Schema::hasTable('hrd_presensi_izinterlambat')) {
            Schema::create('hrd_presensi_izinterlambat', function (Blueprint $table) {
                $table->id();
                $table->integer('id_presensi');
                $table->string('kode_izin_terlambat', 20);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('hrd_presensi_izinkeluar')) {
            Schema::create('hrd_presensi_izinkeluar', function (Blueprint $table) {
                $table->id();
                $table->integer('id_presensi');
                $table->string('kode_izin_keluar', 20);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('hrd_presensi_izinpulang')) {
            Schema::create('hrd_presensi_izinpulang', function (Blueprint $table) {
                $table->id();
                $table->integer('id_presensi');
                $table->string('kode_izin_pulang', 20);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('hrd_presensi_izincuti')) {
            Schema::create('hrd_presensi_izincuti', function (Blueprint $table) {
                $table->id();
                $table->integer('id_presensi');
                $table->string('kode_izin_cuti', 20);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('hrd_presensi_izinsakit')) {
            Schema::create('hrd_presensi_izinsakit', function (Blueprint $table) {
                $table->id();
                $table->integer('id_presensi');
                $table->string('kode_izin_sakit', 20);
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('hrd_presensi_izinsakit');
        Schema::dropIfExists('hrd_presensi_izincuti');
        Schema::dropIfExists('hrd_presensi_izinpulang');
        Schema::dropIfExists('hrd_presensi_izinkeluar');
        Schema::dropIfExists('hrd_presensi_izinterlambat');

        Schema::dropIfExists('hrd_izincuti_disposisi');
        Schema::dropIfExists('hrd_izinsakit_disposisi');
        Schema::dropIfExists('hrd_izinabsen_disposisi');

        Schema::dropIfExists('hrd_izindinas');
        Schema::dropIfExists('hrd_izinterlambat');
        Schema::dropIfExists('hrd_izinpulang');
        Schema::dropIfExists('hrd_izinkeluar');
        Schema::dropIfExists('hrd_izincuti');
        Schema::dropIfExists('hrd_izinsakit');
        Schema::dropIfExists('hrd_izinabsen');
        Schema::dropIfExists('hrd_jeniscuti_khusus');
        Schema::dropIfExists('hrd_jeniscuti');
    }
};
