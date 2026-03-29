<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Warga extends Model
{
    protected $fillable = [
        'no_rumah',
        'blok',
        'nama',
        'telp',
    ];

    public function iurans()
    {
        return $this->hasMany(Iuran::class);
    }
}
