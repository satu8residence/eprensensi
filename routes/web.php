<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CabangController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DepartemenController;
use App\Http\Controllers\KaryawanController;
use App\Http\Controllers\KonfigurasiController;
use App\Http\Controllers\PengajuanizinController;
use App\Http\Controllers\PinjamanController;
use App\Http\Controllers\PinjamnaController;
use App\Http\Controllers\PresensiController;
use App\Http\Controllers\SlipgajiController;
use App\Http\Controllers\SetjadwalController;
use App\Http\Controllers\LemburController;
use App\Http\Controllers\UtilityController;
use App\Http\Controllers\CutiController;
use App\Http\Controllers\HakcutiController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Psy\VarDumper\Presenter;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/




Route::middleware(['guest:karyawan'])->group(function () {
    Route::get('/', function () {
        return view('auth.login');
    })->name('login');

    Route::post('/proseslogin', [AuthController::class, 'proseslogin']);
});


Route::middleware(['guest:user'])->group(function () {
    Route::get('/panel', function () {
        return view('auth.loginadmin');
    })->name('loginadmin');

    Route::post('/prosesloginadmin', [AuthController::class, 'prosesloginadmin']);
});
Route::middleware(['auth:karyawan'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index']);
    Route::get('/proseslogout', [AuthController::class, 'proseslogout']);

    //Presensi
    Route::get('/presensi/create', [PresensiController::class, 'create']);
    Route::post('/presensi/store', [PresensiController::class, 'store']);
    Route::get('/presensi/idcard', [PresensiController::class, 'idcard']);
    Route::post('/presensi/storewithcamera', [PresensiController::class, 'storewithcamera']);

    //Edit Profile
    Route::get('/editprofile', [PresensiController::class, 'editprofile']);
    Route::post('/presensi/{nik}/updateprofile', [PresensiController::class, 'updateprofile']);

    //Histori
    Route::get('/presensi/histori', [PresensiController::class, 'histori']);
    Route::post('/gethistori', [PresensiController::class, 'gethistori']);

    //Izin
    Route::get('/presensi/izin', [PresensiController::class, 'izin']);
    Route::get('/presensi/buatizin', [PresensiController::class, 'buatizin']);
    Route::post('/presensi/storeizin', [PresensiController::class, 'storeizin']);
    Route::post('/presensi/cekpengajuanizin', [PresensiController::class, 'cekpengajuanizin']);
    Route::get('/izin/{id}/showact', [PresensiController::class, 'showactizin']);
    Route::get('/izin/{id}/editizin', [PresensiController::class, 'editizin']);
    Route::post('/izin/{id}/update', [PresensiController::class, 'updateizin']);
    Route::get('/izin/{id}/delete', [PresensiController::class, 'deleteizin']);
    Route::get('/izin/{id}/showsid', [PresensiController::class, 'showsid']);


    //Pinjaman

    Route::get('/pinjaman', [PinjamanController::class, 'index']);
    Route::get('/pinjaman/{no_pinjaman}/show', [PinjamanController::class, 'show']);
    Route::get('/pinjaman/simulasi', [PinjamanController::class, 'simulasi']);

    //Slip Gaji
    Route::get('/slipgaji', [SlipgajiController::class, 'index']);
    Route::get('/slipgaji/{bulan}/{tahun}/cetak', [SlipgajiController::class, 'cetak']);
    Route::get('/slipgaji/{bulan}/{tahun}/cetakslipgaji', [SlipgajiController::class, 'cetakslipgaji']);
    Route::get('/slipgaji/{bulan}/{tahun}/cetakthr', [SlipgajiController::class, 'cetakthr']);

    Route::get('/pengajuanizin/createizinterlambat', [PengajuanizinController::class, 'createizinterlambat']);
    Route::get('/pengajuanizin/createizinabsen', [PengajuanizinController::class, 'createizinabsen']);
    Route::get('/pengajuanizin/createizinkeluar', [PengajuanizinController::class, 'createizinkeluar']);
    Route::get('/pengajuanizin/createizinpulang', [PengajuanizinController::class, 'createizinpulang']);
    Route::get('/pengajuanizin/createsakit', [PengajuanizinController::class, 'createsakit']);
    Route::get('/pengajuanizin/createcuti', [PengajuanizinController::class, 'createcuti']);
    Route::post('/pengajuanizin/store', [PengajuanizinController::class, 'store']);
    Route::post('/pengajuanizin/storesakit', [PengajuanizinController::class, 'storesakit']);
    Route::post('/pengajuanizin/storecuti', [PengajuanizinController::class, 'storecuti']);
});


Route::middleware(['auth:user'])->group(function () {
    Route::get('/proseslogoutadmin', [AuthController::class, 'proseslogoutadmin']);
    Route::get('/panel/dashboardadmin', [DashboardController::class, 'dashboardadmin']);

    //Karyawan
    Route::get('/karyawan', [KaryawanController::class, 'index']);
    Route::post('/karyawan/store', [KaryawanController::class, 'store']);
    Route::post('/karyawan/edit', [KaryawanController::class, 'edit']);
    Route::post('/karyawan/{nik}/update', [KaryawanController::class, 'update']);
    Route::post('/karyawan/{nik}/delete', [KaryawanController::class, 'delete']);
    Route::post('/karyawan/{nik}/resetpassword', [KaryawanController::class, 'resetpassword']);

    //Departemen
    Route::get('/departemen', [DepartemenController::class, 'index']);
    Route::post('/departemen/store', [DepartemenController::class, 'store']);
    Route::post('/departemen/edit', [DepartemenController::class, 'edit']);
    Route::post('/departemen/{kode_dept}/update', [DepartemenController::class, 'update']);
    Route::post('/departemen/{kode_dept}/delete', [DepartemenController::class, 'delete']);

    //Cuti Master
    Route::get('/cuti', [CutiController::class, 'index']);
    Route::post('/cuti/store', [CutiController::class, 'store']);
    Route::post('/cuti/edit', [CutiController::class, 'edit']);
    Route::post('/cuti/{kode_cuti}/update', [CutiController::class, 'update']);
    Route::post('/cuti/{kode_cuti}/delete', [CutiController::class, 'delete']);

    //Hak Cuti Karyawan
    Route::get('/hakcuti', [HakcutiController::class, 'index']);
    Route::post('/hakcuti/store', [HakcutiController::class, 'store']);
    Route::post('/hakcuti/edit', [HakcutiController::class, 'edit']);
    Route::post('/hakcuti/{id}/update', [HakcutiController::class, 'update']);
    Route::post('/hakcuti/{id}/delete', [HakcutiController::class, 'delete']);

    //Presensi
    Route::get('/presensi/monitoring', [PresensiController::class, 'monitoring']);

    Route::post('/getpresensi', [PresensiController::class, 'getpresensi']);
    Route::post('/tampilkanpeta', [PresensiController::class, 'tampilkanpeta']);
    Route::get('/presensi/laporan', [PresensiController::class, 'laporan']);
    Route::post('/presensi/cetaklaporan', [PresensiController::class, 'cetaklaporan']);
    Route::get('/presensi/laporanlembur', [PresensiController::class, 'laporanlembur']);
    Route::post('/presensi/cetaklaporanlembur', [PresensiController::class, 'cetaklaporanlembur']);
    Route::get('/presensi/laporancuti', [PresensiController::class, 'laporancuti']);
    Route::post('/presensi/cetaklaporancuti', [PresensiController::class, 'cetaklaporancuti']);
    Route::get('/presensi/rekap', [PresensiController::class, 'rekap']);
    Route::post('/presensi/cetakrekap', [PresensiController::class, 'cetakrekap']);
    Route::get('/presensi/izinsakit', [PresensiController::class, 'izinsakit']);
    Route::post('/presensi/approveizinsakit', [PresensiController::class, 'approveizinsakit']);
    Route::get('/presensi/{id}/batalkanizinsakit', [PresensiController::class, 'batalkanizinsakit']);



    //Cabang
    Route::get('/cabang', [CabangController::class, 'index']);
    Route::post('/cabang/store', [CabangController::class, 'store']);
    Route::post('/cabang/edit', [CabangController::class, 'edit']);
    Route::post('/cabang/update', [CabangController::class, 'update']);
    Route::post('/cabang/{kode_cabang}/delete', [CabangController::class, 'delete']);



    //Konfigurasi

    Route::get('/konfigurasi/lokasikantor', [KonfigurasiController::class, 'lokasikantor']);
    Route::post('/konfigurasi/updatelokasikantor', [KonfigurasiController::class, 'updatelokasikantor']);

    Route::get('/konfigurasi/jamkerja', [KonfigurasiController::class, 'jamkerja']);
    Route::post('/konfigurasi/storejamkerja', [KonfigurasiController::class, 'storejamkerja']);
    Route::post('/konfigurasi/editjamkerja', [KonfigurasiController::class, 'editjamkerja']);
    Route::post('/konfigurasi/updatejamkerja', [KonfigurasiController::class, 'updatejamkerja']);
    Route::post('/konfigurasi/{kode_jam_kerja}/delete', [KonfigurasiController::class, 'deletejamkerja']);

    // Set Jadwal Shift Karyawan (Weekly Shift Schedule)
    Route::get('/konfigurasi/setjadwal', [SetjadwalController::class, 'index']);
    Route::post('/konfigurasi/setjadwal/store', [SetjadwalController::class, 'store']);
    Route::get('/konfigurasi/setjadwal/download-template', [SetjadwalController::class, 'downloadTemplate']);
    Route::post('/konfigurasi/setjadwal/{kode_setjadwal}/delete', [SetjadwalController::class, 'delete']);

    // Set Jam Kerja Karyawan (Daily & By-Date Config)
    Route::post('/karyawan/{nik}/setjamkerja', [KaryawanController::class, 'setjamkerja']);
    Route::post('/karyawan/{nik}/setjamkerja/store', [KaryawanController::class, 'storesetjamkerja']);
    Route::post('/karyawan/{nik}/setjamkerja/storebydate', [KaryawanController::class, 'storesetjamkerjabydate']);
    Route::post('/karyawan/setjamkerja/{id}/deletebydate', [KaryawanController::class, 'deletesetjamkerjabydate']);

    // Lembur (Overtime) CRUD
    Route::get('/lembur', [LemburController::class, 'index']);
    Route::post('/lembur/store', [LemburController::class, 'store']);
    Route::post('/lembur/edit', [LemburController::class, 'edit']);
    Route::post('/lembur/{kode_lembur}/update', [LemburController::class, 'update']);
    Route::post('/lembur/{kode_lembur}/delete', [LemburController::class, 'delete']);
    Route::post('/lembur/{kode_lembur}/approve', [LemburController::class, 'approve']);
    Route::post('/lembur/{kode_lembur}/reject', [LemburController::class, 'reject']);
    Route::post('/lembur/getkaryawanbydept', [LemburController::class, 'getkaryawanbydept']);

    // Utilities - Bersihkan Foto
    Route::get('/bersihkanfoto', [UtilityController::class, 'index']);
    Route::post('/bersihkanfoto', [UtilityController::class, 'process']);
});

Route::post('/storefrommachine', [PresensiController::class, 'storefrommachine']);
