<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Warga;
use App\Models\Iuran;
use App\Models\Kas;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class IuranController extends Controller
{
    public function index(Request $request)
    {
        $tahun = $request->input('tahun', date('Y'));
        $warga = Warga::with(['iurans' => function($query) use ($tahun) {
            $query->where('tahun', $tahun);
        }])->orderBy('no_rumah')->get();

        return Inertia::render('Iuran/Index', [
            'warga' => $warga,
            'tahun' => (int) $tahun,
            'amount' => config('sgv.resident_fee'),
            'can_edit' => auth()->check(),
        ]);
    }

    public function dashboardIndex(Request $request)
    {
        $tahun = $request->input('tahun', date('Y'));
        $warga = Warga::with(['iurans' => function($query) use ($tahun) {
            $query->where('tahun', $tahun);
        }])->orderBy('no_rumah')->get();
        $websetting = json_decode(file_get_contents(storage_path('app/private/private/web-setting.json')) , true);
        return Inertia::render('Dashboard/Iuran/Index', [
            'warga' => $warga,
            'tahun' => (int) $tahun,
            'amount' =>(int) $websetting['iuran_bulanan'] ,
        ]);
    }

    public function toggle(Request $request)
    {
        if (!auth()->check()) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $request->validate([
            'warga_id' => 'required|exists:wargas,id',
            'bulan' => 'required|integer|min:1|max:12',
            'tahun' => 'required|integer',
        ]);

        return DB::transaction(function () use ($request) {
            $iuran = Iuran::where('warga_id', $request->warga_id)
                ->where('bulan', $request->bulan)
                ->where('tahun', $request->tahun)
                ->first();

            if ($iuran) {
                // Uncheck: Delete Iuran and Kas
                $iuran->kas()->delete();
                $iuran->delete();
                return redirect()->route('dashboard.iuran.index')->with('success','Pembayaran berhasil di batalkan');
            } else {
                // Check: Create Iuran and Kas
                $iuran = Iuran::create([
                    'warga_id' => $request->warga_id,
                    'bulan' => $request->bulan,
                    'tahun' => $request->tahun,
                    'jumlah' => config('sgv.resident_fee'),
                    'tanggal_bayar' => now(),
                ]);

                $iuran->kas()->create([
                    'type' => 'masuk',
                    'kategori' => 'Iuran Warga',
                    'jumlah' => $iuran->jumlah,
                    'keterangan' => "Iuran {$iuran->warga->nama} ({$iuran->warga->no_rumah}) - Bulan {$iuran->bulan}/{$iuran->tahun}",
                    'tanggal' => now(),
                ]);

                return redirect()->route('dashboard.iuran.index')->with('success','Pembayaran berhasil');
            }
        });
    }
}
