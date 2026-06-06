<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    protected $table = 'transaksi';

    protected $primaryKey = 'id_transaksi';

    protected $fillable = [
        'id_user',
        'no_trx',
        'nomor_kios',
        'jenis_pajak',
        'metode_pembayaran',
        'nominal',
        'tanggal',
        'status',
        'catatan_verifikasi',
        'verified_by',
        'verified_at',
        'keterangan',
    ];

    protected $casts = [
        'tanggal'     => 'datetime',
        'verified_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by', 'id_user');
    }
}