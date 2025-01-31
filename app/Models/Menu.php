<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
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
    ];
    

    // protected $fillable = [
    //     'tipe',
    //     'nama_makanan',
    //     'kalori',
    // ];

    /**
     * Relasi ke FoodConsumption (Opsional).
     */
    // public function foodConsumptions()
    // {
    //     return $this->hasMany(FoodConsumption::class);
    // }
}
