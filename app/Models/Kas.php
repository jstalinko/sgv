<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kas extends Model
{
    protected $fillable = [
        'type',
        'kategori',
        'jumlah',
        'keterangan',
        'tanggal',
        'ref_id',
        'ref_type',
        'bukti',
    ];

    public function ref()
    {
        return $this->morphTo();
    }
}
