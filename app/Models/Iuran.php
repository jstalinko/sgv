<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Iuran extends Model
{
    protected $fillable = [
        'warga_id',
        'bulan',
        'tahun',
        'jumlah',
        'tanggal_bayar',
    ];

    public function warga()
    {
        return $this->belongsTo(Warga::class);
    }

    public function kas()
    {
        return $this->morphOne(Kas::class, 'ref');
    }
}
