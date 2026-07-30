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
        Schema::create('hrd_lembur', function (Blueprint $table) {
            $table->string('kode_lembur', 50)->primary();
            $table->date('tanggal');
            $table->dateTime('tanggal_dari');
            $table->dateTime('tanggal_sampai');
            $table->string('kode_cabang', 10)->nullable();
            $table->string('kode_dept', 10)->nullable();
            $table->text('keterangan')->nullable();
            $table->tinyInteger('kategori')->default(1); // 1 = Hari Kerja, 2 = Hari Libur
            $table->tinyInteger('istirahat')->default(0); // 0 = Tidak, 1 = Ya
            $table->tinyInteger('status')->default(0);    // 0 = Pending, 1 = Approved, 2 = Rejected
            $table->timestamps();
        });

        Schema::create('hrd_lembur_detail', function (Blueprint $table) {
            $table->id();
            $table->string('kode_lembur', 50);
            $table->string('nik', 12);
            $table->timestamps();

            $table->foreign('kode_lembur')->references('kode_lembur')->on('hrd_lembur')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('hrd_lembur_detail');
        Schema::dropIfExists('hrd_lembur');
    }
};
