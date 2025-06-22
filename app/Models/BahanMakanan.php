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
        'standard_portion_value', // <--- TAMBAHKAN INI
        'standard_portion_unit',
    ];

    public function menus()
    {
        return $this->belongsToMany(Menu::class, 'bahan_menu', 'bahan_makanan_id','bahan_menu')
            ->withPivot('jumlah')
            ->withTimestamps();
    }

    public function riwayatStok(): HasMany
    {
        return $this->hasMany(RiwayatStokBahanMakanan::class, 'bahan_makanan_id');
    }

    // public function getKaloriPer100gAttribute()
    // {
    //     // Kalori = (Protein * 4) + (Karbohidrat * 4) + (Lemak * 9)
    //     return ($this->protein * 4) + ($this->karbohidrat * 4) + ($this->total_lemak * 9);
    // }
}
