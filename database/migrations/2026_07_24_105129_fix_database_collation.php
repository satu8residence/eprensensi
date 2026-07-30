<?php

use Illuminate\Database\Migrations\Migration;
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
        // Alter database default charset and collation
        DB::statement("ALTER DATABASE eprensensi CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");

        // List of tables to convert
        $tables = [
            'cabang',
            'departemen',
            'karyawan',
            'konfigurasi_lokasi',
            'presensi',
            'pengajuan_izin',
            'users'
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                DB::statement("ALTER TABLE `$table` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Convert back to utf8mb4_0900_ai_ci if needed
        DB::statement("ALTER DATABASE eprensensi CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci");

        $tables = [
            'cabang',
            'departemen',
            'karyawan',
            'konfigurasi_lokasi',
            'presensi',
            'pengajuan_izin',
            'users'
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                DB::statement("ALTER TABLE `$table` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci");
            }
        }
    }
};
