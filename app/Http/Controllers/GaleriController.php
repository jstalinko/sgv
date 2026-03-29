<?php

namespace App\Http\Controllers;

use App\Models\Galeri;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;

class GaleriController extends Controller
{
    public function index()
    {
        $galeri = Galeri::orderByDesc('created_at')->get();
        return Inertia::render('Galeri/Index', [
            'galeri' => $galeri
        ]);
    }

    public function dashboardIndex()
    {
        $galeri = Galeri::orderByDesc('created_at')->paginate(20);
        return Inertia::render('Dashboard/Galeri/Index', [
            'galeri' => $galeri
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.judul' => 'required|string|max:255',
            'items.*.deskripsi' => 'nullable|string',
            'items.*.gambar' => 'required|image|max:5120',
        ]);

        foreach ($request->file('items') as $index => $item) {
            $path = $item['gambar']->store('galeri', 'public');
            Galeri::create([
                'judul' => $request->input("items.{$index}.judul"),
                'deskripsi' => $request->input("items.{$index}.deskripsi"),
                'gambar' => $path,
            ]);
        }

        return redirect()->back()->with('message', count($request->file('items')) . ' foto berhasil diunggah ke galeri');
    }

    public function update(Request $request, Galeri $galeri)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'gambar' => 'nullable|image|max:5120',
        ]);

        $data = $request->only(['judul', 'deskripsi']);

        if ($request->hasFile('gambar')) {
            if ($galeri->gambar) {
                Storage::disk('public')->delete($galeri->gambar);
            }
            $data['gambar'] = $request->file('gambar')->store('galeri', 'public');
        }

        $galeri->update($data);

        return redirect()->back()->with('message', 'Foto galeri berhasil diperbarui');
    }

    public function destroy(Galeri $galeri)
    {
        if ($galeri->gambar) {
            Storage::disk('public')->delete($galeri->gambar);
        }
        $galeri->delete();

        return redirect()->back()->with('message', 'Foto galeri berhasil dihapus');
    }
}
