<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;

class UtilityController extends Controller
{
    public function index()
    {
        return view('utility.bersihkanfoto');
    }

    public function process(Request $request)
    {
        $request->validate([
            'dari' => 'required|date',
            'sampai' => 'required|date|after_or_equal:dari',
        ]);

        $dari = $request->dari;
        $sampai = $request->sampai;

        try {
            DB::beginTransaction();

            $presensi = DB::table('presensi')
                ->whereBetween('tgl_presensi', [$dari, $sampai])
                ->where(function ($query) {
                    $query->whereNotNull('foto_in')
                        ->orWhereNotNull('foto_out');
                })
                ->get();

            $deletedCount = 0;

            foreach ($presensi as $p) {
                $updateData = [];

                if (!empty($p->foto_in)) {
                    $file_in = 'public/uploads/absensi/' . $p->foto_in;
                    if (Storage::exists($file_in)) {
                        Storage::delete($file_in);
                    }
                    $updateData['foto_in'] = null;
                    $deletedCount++;
                }

                if (!empty($p->foto_out)) {
                    $file_out = 'public/uploads/absensi/' . $p->foto_out;
                    if (Storage::exists($file_out)) {
                        Storage::delete($file_out);
                    }
                    $updateData['foto_out'] = null;
                    $deletedCount++;
                }

                if (!empty($updateData)) {
                    DB::table('presensi')->where('id', $p->id)->update($updateData);
                }
            }

            DB::commit();
            return Redirect::back()->with(['success' => 'Berhasil menghapus ' . $deletedCount . ' file foto presensi dari periode ' . date('d-m-Y', strtotime($dari)) . ' s/d ' . date('d-m-Y', strtotime($sampai))]);
        } catch (\Exception $e) {
            DB::rollBack();
            return Redirect::back()->with(['warning' => 'Gagal menghapus foto presensi: ' . $e->getMessage()]);
        }
    }
}
