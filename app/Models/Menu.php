<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'deskripsi',
        'tipe_pasien',
        'gambar',
        'total_protein',
        'total_karbohidrat',
        'total_lemak',
    ];

    public function bahanMakanans()
    {
        return $this->belongsToMany(BahanMakanan::class, 'bahan_menu')
            ->withPivot('jumlah')
            ->withTimestamps();
    }

    public function jadwalMakanans()
    {
        return $this->belongsToMany(JadwalMakanan::class, 'jadwal_makanan_menu')
                    ->withPivot('tanggal', 'waktu_makan')
                    ->withTimestamps();
    }

}
