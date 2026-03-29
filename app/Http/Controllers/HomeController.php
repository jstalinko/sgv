<?php

namespace App\Http\Controllers;

use App\Models\Galeri;
use App\Models\Kegiatan;
use App\Models\Warga;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;
class HomeController extends Controller
{
    public function index()
    {
        return Inertia::render('Index', [
            'props' => [
                'warga' => Warga::count(),
                'kegiatan' => Kegiatan::count(),
                'kegiatan_mendatang' => Kegiatan::where('tanggal', '>', now())->orderBy('tanggal')->first(),
                'galeri' => Galeri::orderBy('id', 'desc')->get(),
            ]
        ]);
    }
}
