<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Meja extends Model
{
    use HasFactory;
    use HasUuids;

    protected $table = 'mejas';

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'nomor_meja',
        'qr_code',
    ];

    public function pesanans()
    {
        return $this->hasMany(Pesanan::class, 'id_meja');
    }
}
