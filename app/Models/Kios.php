<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kios extends Model
{
    protected $table = 'kios';

    protected $primaryKey = 'id_kios';

    protected $fillable = [
        'no_kios',
        'lokasi',
        'ukuran',
        'tarif_bulanan',
        'status',
        'keterangan',
    ];
}