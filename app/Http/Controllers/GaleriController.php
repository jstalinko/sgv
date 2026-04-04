<?php

namespace App\Http\Controllers;

use App\Models\Galeri;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Traits\CompressesImages;

class GaleriController extends Controller
{
    use CompressesImages;

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
            'items.*.gambar' => 'required|file|max:10240|mimes:jpg,jpeg,png,gif,bmp,tiff,tif,heic,heif,webp',
        ]);

        foreach ($request->file('items') as $index => $item) {
            $path = $this->compressToWebp($item['gambar']);
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
            'gambar' => 'nullable|file|max:10240|mimes:jpg,jpeg,png,gif,bmp,tiff,tif,heic,heif,webp',
        ]);

        $data = $request->only(['judul', 'deskripsi']);

        if ($request->hasFile('gambar')) {
            if ($galeri->gambar) {
                Storage::disk('public')->delete($galeri->gambar);
            }
            $data['gambar'] = $this->compressToWebp($request->file('gambar'));
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

