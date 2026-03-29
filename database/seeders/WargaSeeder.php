<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Warga;

class WargaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $dataWarga = [
            ['no_rumah' => 'A1',  'blok' => 'Blok A', 'nama' => 'Bp. Sugimin'],
            ['no_rumah' => 'A2',  'blok' => 'Blok A', 'nama' => 'Bp. Ridho'],
            ['no_rumah' => 'A3',  'blok' => 'Blok A', 'nama' => 'Bp. Duta'],
            ['no_rumah' => 'A3a', 'blok' => 'Blok A', 'nama' => 'Ibu Ketut'],
            ['no_rumah' => 'A5',  'blok' => 'Blok A', 'nama' => 'Mas Ryan'],
            ['no_rumah' => 'A6',  'blok' => 'Blok A', 'nama' => 'Bp. Sis'],
            ['no_rumah' => 'A7',  'blok' => 'Blok A', 'nama' => 'Ibu Sukarti'],
            ['no_rumah' => 'A8',  'blok' => 'Blok A', 'nama' => 'Bp. Imron'],
            ['no_rumah' => 'A9',  'blok' => 'Blok A', 'nama' => 'Bp. Dobby'],

            ['no_rumah' => 'B1',  'blok' => 'Blok B', 'nama' => 'Bp. Anam'],
            ['no_rumah' => 'B2',  'blok' => 'Blok B', 'nama' => 'Bp. Alin'],
            ['no_rumah' => 'B3',  'blok' => 'Blok B', 'nama' => 'Bp. Burnadi'],
            ['no_rumah' => 'B3a', 'blok' => 'Blok B', 'nama' => 'Bp. Lukman'],
            ['no_rumah' => 'B5',  'blok' => 'Blok B', 'nama' => 'Bp. Nanang'],
            ['no_rumah' => 'B6',  'blok' => 'Blok B', 'nama' => 'Bp. Febi'],
            ['no_rumah' => 'B7',  'blok' => 'Blok B', 'nama' => 'Bp. Alfiandi'],
            ['no_rumah' => 'B8',  'blok' => 'Blok B', 'nama' => 'Bp. Ubay'],

            ['no_rumah' => 'C1',  'blok' => 'Blok C', 'nama' => 'Ibu Desi'],
            ['no_rumah' => 'C2',  'blok' => 'Blok C', 'nama' => 'Ibu Ida'],
            ['no_rumah' => 'C3',  'blok' => 'Blok C', 'nama' => 'Bp. Lion'],
            ['no_rumah' => 'C3a', 'blok' => 'Blok C', 'nama' => 'Bp. Wafa'],
            ['no_rumah' => 'C5',  'blok' => 'Blok C', 'nama' => 'Bp. Faruq'],
            ['no_rumah' => 'C6',  'blok' => 'Blok C', 'nama' => 'Bp. Arman'],
            ['no_rumah' => 'C7',  'blok' => 'Blok C', 'nama' => 'Bp. Chandra'],

            ['no_rumah' => 'D1',  'blok' => 'Blok D', 'nama' => 'Bp. Dede'],
            ['no_rumah' => 'D2',  'blok' => 'Blok D', 'nama' => 'Bp. Redo'],
            ['no_rumah' => 'D3',  'blok' => 'Blok D', 'nama' => 'Bp. Iswandi'],
        ];

        foreach ($dataWarga as $warga) {
            Warga::create([
                'no_rumah' => $warga['no_rumah'],
                'blok' => $warga['blok'],
                'nama' => $warga['nama'],
                'telp' => '08' . rand(1111111111, 9999999999), // random telp
            ]);
        }
    }
}