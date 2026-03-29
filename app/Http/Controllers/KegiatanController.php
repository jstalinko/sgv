<?php

namespace App\Http\Controllers;

use App\Models\Kegiatan;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;

class KegiatanController extends Controller
{
    public function index()
    {
        $kegiatan = Kegiatan::orderByDesc('tanggal')->get()->map(function($item) {
            $item->status = $item->tanggal < now() ? 'past' : 'upcoming';
            return $item;
        });

        return Inertia::render('Kegiatan/Index', [
            'kegiatan' => $kegiatan
        ]);
    }

    public function jadwalIndex()
    {
        // Upcoming: date >= now, sorted soonest first
        $upcoming = Kegiatan::where('tanggal', '>=', now())
            ->orderBy('tanggal', 'asc')
            ->get()
            ->each(fn($k) => $k->status = 'upcoming');

        // Past: date < now, most recent first
        $past = Kegiatan::where('tanggal', '<', now())
            ->orderBy('tanggal', 'desc')
            ->get()
            ->each(fn($k) => $k->status = 'past');

        return Inertia::render('Jadwal/Index', [
            'upcoming' => $upcoming,
            'past' => $past,
        ]);
    }

    public function dashboardIndex()
    {
        $kegiatan = Kegiatan::orderByDesc('tanggal')->paginate(10);
        
        return Inertia::render('Dashboard/Kegiatan/Index', [
            'kegiatan' => $kegiatan
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'tanggal' => 'required|date',
            'gambar' => 'nullable|image|max:2048',
        ]);

        $path = null;
        if ($request->hasFile('gambar')) {
            $path = $request->file('gambar')->store('kegiatan', 'public');
        }

        Kegiatan::create([
            'judul' => $request->judul,
            'deskripsi' => $request->deskripsi,
            'tanggal' => $request->tanggal,
            'gambar' => $path,
        ]);

        return redirect()->back()->with('message', 'Kegiatan berhasil ditambahkan');
    }

    public function update(Request $request, Kegiatan $kegiatan)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'tanggal' => 'required|date',
            'gambar' => 'nullable|image|max:2048',
        ]);

        $data = $request->only(['judul', 'deskripsi', 'tanggal']);

        if ($request->hasFile('gambar')) {
            if ($kegiatan->gambar) {
                Storage::disk('public')->delete($kegiatan->gambar);
            }
            $data['gambar'] = $request->file('gambar')->store('kegiatan', 'public');
        }

        $kegiatan->update($data);

        return redirect()->back()->with('message', 'Kegiatan berhasil diperbarui');
    }

    public function destroy(Kegiatan $kegiatan)
    {
        if ($kegiatan->gambar) {
            Storage::disk('public')->delete($kegiatan->gambar);
        }
        $kegiatan->delete();

        return redirect()->back()->with('message', 'Kegiatan berhasil dihapus');
    }
}
