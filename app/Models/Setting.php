<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $table = 'settings'; // FIX: bukan 'setting_sistem'

    protected $primaryKey = 'id_setting';

    protected $fillable = [
        'nama_setting',
        'nilai',
        'deskripsi',
    ];
}