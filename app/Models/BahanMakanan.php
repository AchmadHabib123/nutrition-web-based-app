<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BahanMakanan extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'gambar',
        'protein',
        'karbohidrat',
        'total_lemak',
        'tipe_pasien',
        'kategori_bahan_masakan',
        'stok',
    ];

    public function menus()
    {
        return $this->belongsToMany(Menu::class, 'bahan_menu')
            ->withPivot('jumlah')
            ->withTimestamps();
    }

    public function riwayatStok(): HasMany
    {
        return $this->hasMany(RiwayatStokBahanMakanan::class, 'bahan_makanan_id');
    }
}
