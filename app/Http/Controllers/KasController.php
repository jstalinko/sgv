<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Kas;
use Inertia\Inertia;

class KasController extends Controller
{
    public function index(Request $request)
    {
        $kas = Kas::orderByDesc('tanggal')->orderByDesc('created_at')->paginate(20);
        
        $totalMasuk = Kas::where('type', 'masuk')->sum('jumlah');
        $totalKeluar = Kas::where('type', 'keluar')->sum('jumlah');
        $saldo = $totalMasuk - $totalKeluar;

        return Inertia::render('Kas/Index', [
            'kas' => $kas,
            'totalMasuk' => (float) $totalMasuk,
            'totalKeluar' => (float) $totalKeluar,
            'saldo' => (float) $saldo,
            'can_edit' => auth()->check(),
        ]);
    }

    public function dashboardIndex(Request $request)
    {
        $kas = Kas::orderByDesc('tanggal')->orderByDesc('created_at')->paginate(20);
        
        $totalMasuk = Kas::where('type', 'masuk')->sum('jumlah');
        $totalKeluar = Kas::where('type', 'keluar')->sum('jumlah');
        $saldo = $totalMasuk - $totalKeluar;

        return Inertia::render('Dashboard/Kas/Index', [
            'kas' => $kas,
            'totalMasuk' => (float) $totalMasuk,
            'totalKeluar' => (float) $totalKeluar,
            'saldo' => (float) $saldo,
        ]);
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
