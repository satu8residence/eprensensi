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
        if (Schema::hasTable('pengajuan_izin')) {
            Schema::table('pengajuan_izin', function (Blueprint $table) {
                if (!Schema::hasColumn('pengajuan_izin', 'dari')) {
                    $table->date('dari')->nullable();
                }
                if (!Schema::hasColumn('pengajuan_izin', 'sampai')) {
                    $table->date('sampai')->nullable();
                }
                if (!Schema::hasColumn('pengajuan_izin', 'jmlhari')) {
                    $table->integer('jmlhari')->nullable();
                }
                if (!Schema::hasColumn('pengajuan_izin', 'sid')) {
                    $table->string('sid', 255)->nullable();
                }
                if (!Schema::hasColumn('pengajuan_izin', 'jenis_izin')) {
                    $table->string('jenis_izin', 50)->nullable();
                }
                if (!Schema::hasColumn('pengajuan_izin', 'jam_pulang')) {
                    $table->time('jam_pulang')->nullable();
                }
                if (!Schema::hasColumn('pengajuan_izin', 'jam_keluar')) {
                    $table->time('jam_keluar')->nullable();
                }
                if (!Schema::hasColumn('pengajuan_izin', 'jenis_cuti')) {
                    $table->string('jenis_cuti', 50)->nullable();
                }
                if (!Schema::hasColumn('pengajuan_izin', 'created_by')) {
                    $table->string('created_by', 50)->nullable();
                }
            });

            // Sync existing records to populate 'dari' and 'sampai' and 'jmlhari'
            DB::table('pengajuan_izin')
                ->whereNull('dari')
                ->update([
                    'dari' => DB::raw('tgl_izin'),
                    'sampai' => DB::raw('tgl_izin'),
                    'jmlhari' => 1
                ]);

            // Sync existing records to populate unique 'kode_izin' if null or empty
            $izins = DB::table('pengajuan_izin')->get();
            foreach ($izins as $index => $izin) {
                if (empty($izin->kode_izin)) {
                    $num = str_pad($index + 1, 4, '0', STR_PAD_LEFT);
                    DB::table('pengajuan_izin')
                        ->where('id', $izin->id)
                        ->update(['kode_izin' => 'IZ-2026-' . $num]);
                }
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
        if (Schema::hasTable('pengajuan_izin')) {
            Schema::table('pengajuan_izin', function (Blueprint $table) {
                $table->dropColumn([
                    'dari', 'sampai', 'jmlhari', 'sid', 'jenis_izin', 'jam_pulang', 'jam_keluar', 'jenis_cuti', 'created_by'
                ]);
            });
        }
    }
};
