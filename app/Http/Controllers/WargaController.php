<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Warga;
use Inertia\Inertia;

class WargaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $warga = Warga::orderBy('blok')->orderBy('no_rumah')->get();
        return Inertia::render('Warga/Index', [
            'warga' => $warga
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'no_rumah' => 'required|string|unique:wargas,no_rumah',
            'blok' => 'required|string',
            'nama' => 'required|string',
            'telp' => 'nullable|string',
        ]);

        Warga::create($validated);

        return redirect()->back()->with('message', 'Warga berhasil ditambahkan');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Warga $warga)
    {
        $validated = $request->validate([
            'no_rumah' => 'required|string|unique:wargas,no_rumah,' . $warga->id,
            'blok' => 'required|string',
            'nama' => 'required|string',
            'telp' => 'nullable|string',
        ]);

        $warga->update($validated);

        return redirect()->back()->with('message', 'Data warga berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Warga $warga)
    {
        $warga->delete();

        return redirect()->back()->with('message', 'Warga berhasil dihapus');
    }
}
