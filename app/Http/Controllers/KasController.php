<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kas;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class KasController extends Controller
{
    private function buildFilteredData(Request $request): array
    {
        $bulan = $request->input('bulan'); // 1-12 or null
        $tahun = $request->input('tahun'); // e.g. 2026 or null
        $isAll = $request->has('all');

        if (!$isAll && !$request->has('bulan') && !$request->has('tahun')) {
            $bulan = date('m');
            $tahun = date('Y');
        }

        $query = Kas::query();

        if ($bulan) {
            // SQLite-compatible: strftime('%m', tanggal) returns zero-padded month
            $query->whereRaw("strftime('%m', tanggal) = ?", [str_pad($bulan, 2, '0', STR_PAD_LEFT)]);
        }
        if ($tahun) {
            $query->whereRaw("strftime('%Y', tanggal) = ?", [(string) $tahun]);
        }

        $kas = (clone $query)->orderByDesc('tanggal')->orderBy('id', 'desc')->paginate(20)->withQueryString();

        // Period-specific totals
        $totalMasuk = (clone $query)->where('type', 'masuk')->sum('jumlah');
        $totalKeluar = (clone $query)->where('type', 'keluar')->sum('jumlah');

        // Saldo is ALWAYS the running all-time balance
        $allMasuk = Kas::where('type', 'masuk')->sum('jumlah');
        $allKeluar = Kas::where('type', 'keluar')->sum('jumlah');
        $saldo = $allMasuk - $allKeluar;

        // Available years from data — SQLite compatible
        $availableYears = Kas::selectRaw("strftime('%Y', tanggal) as year")
            ->groupBy('year')
            ->orderByDesc('year')
            ->pluck('year');

        return [
            'kas' => $kas,
            'totalMasuk' => (float) $totalMasuk,
            'totalKeluar' => (float) $totalKeluar,
            'saldo' => (float) $saldo,
            'filterBulan' => $bulan ? (int) $bulan : null,
            'filterTahun' => $tahun ? (int) $tahun : null,
            'availableYears' => $availableYears,
        ];
    }

    public function index(Request $request)
    {
        return Inertia::render('Kas/Index', array_merge(
            $this->buildFilteredData($request),
            ['can_edit' => auth()->check()]
        ));
    }

    public function dashboardIndex(Request $request)
    {
        return Inertia::render('Dashboard/Kas/Index', $this->buildFilteredData($request));
    }

    public function store(Request $request)
    {
        if (!auth()->check()) {
            return redirect()->back()->with('error', 'Unauthorized');
        }

        $request->validate([
            'type' => 'required|in:masuk,keluar',
            'kategori' => 'required|string',
            'jumlah' => 'required|numeric|min:0',
            'keterangan' => 'nullable|string',
            'tanggal' => 'required|date',
            'bukti' => 'nullable|image|max:2048',
        ]);

        $buktiPath = null;
        if ($request->hasFile('bukti')) {
            $buktiPath = $request->file('bukti')->store('bukti_kas', 'public');
        }

        Kas::create([
            'type' => $request->type,
            'kategori' => $request->kategori,
            'jumlah' => $request->jumlah,
            'keterangan' => $request->keterangan,
            'tanggal' => $request->tanggal,
            'bukti' => $buktiPath,
        ]);

        return redirect()->back()->with('message', 'Transaksi berhasil dicatat');
    }

    public function update(Request $request, Kas $ka)
    {
        if (!auth()->check()) {
            return redirect()->back()->with('error', 'Unauthorized');
        }

        // Prevent direct editing of Iuran-linked transactions from Kas dashboard if it's income
        if ($ka->ref_type === 'App\Models\Iuran') {
            return redirect()->back()->with('error', 'Transaksi iuran harus dikelola melalui menu Iuran');
        }

        $request->validate([
            'type' => 'required|in:masuk,keluar',
            'kategori' => 'required|string',
            'jumlah' => 'required|numeric|min:0',
            'keterangan' => 'nullable|string',
            'tanggal' => 'required|date',
            'bukti' => 'nullable|image|max:2048',
        ]);

        $data = $request->except('bukti');
        
        if ($request->hasFile('bukti')) {
            // Delete old proof if exists
            if ($ka->bukti) {
                \Storage::disk('public')->delete($ka->bukti);
            }
            $data['bukti'] = $request->file('bukti')->store('bukti_kas', 'public');
        }

        $ka->update($data);

        return redirect()->back()->with('message', 'Transaksi berhasil diperbarui');
    }

    public function destroy(Kas $ka)
    {
        if (!auth()->check()) {
            return redirect()->back()->with('error', 'Unauthorized');
        }

        if ($ka->ref_type === 'App\Models\Iuran') {
            return redirect()->back()->with('error', 'Transaksi iuran harus dikelola melalui menu Iuran');
        }

        if ($ka->bukti) {
            \Storage::disk('public')->delete($ka->bukti);
        }

        $ka->delete();

        return redirect()->back()->with('message', 'Transaksi berhasil dihapus');
    }
}
