<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Table for daily custom shifts (Senin-Minggu)
        Schema::create('konfigurasi_jk_karyawan', function (Blueprint $table) {
            $table->string('nik', 12);
            $table->string('hari', 10);
            $table->char('kode_jam_kerja', 10);
            $table->timestamps();

            $table->primary(['nik', 'hari']);
        });

        // Table for custom shifts by specific dates (By Date)
        Schema::create('konfigurasi_jk_karyawan_by_date', function (Blueprint $table) {
            $table->id();
            $table->string('nik', 12);
            $table->date('tanggal');
            $table->char('kode_jam_kerja', 10);
            $table->timestamps();

            $table->unique(['nik', 'tanggal']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('konfigurasi_jk_karyawan_by_date');
        Schema::dropIfExists('konfigurasi_jk_karyawan');
    }
};
