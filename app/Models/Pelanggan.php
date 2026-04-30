<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pelanggan extends Model
{
    protected $table = 'pelanggans';

    protected $fillable = [
        'nama_pelanggan',
    ];

    public function pesanans()
    {
        return $this->hasMany(Pesanan::class, 'id_pelanggan');
    }
}
