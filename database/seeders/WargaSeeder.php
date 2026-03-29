<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Warga;

class WargaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $blocks = ['A', 'B', 'C'];
        foreach ($blocks as $block) {
            for ($i = 1; $i <= 5; $i++) {
                Warga::create([
                    'no_rumah' => $block . $i,
                    'blok' => 'Blok ' . $block,
                    'nama' => 'Warga ' . $block . $i,
                    'telp' => '0812' . rand(100000, 999999),
                ]);
            }
        }
    }
}
