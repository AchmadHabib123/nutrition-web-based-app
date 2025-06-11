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
        'kalori',
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
    protected static function booted()
    {
        static::creating(function (Menu $menu) {
            $menu->calculateCalories();
        });

        static::updating(function (Menu $menu) {
            // Pastikan untuk menghitung ulang hanya jika komponen makronutrien berubah
            if ($menu->isDirty(['total_protein', 'total_karbohidrat', 'total_lemak'])) {
                $menu->calculateCalories();
            }
        });
    }

    /**
     * Calculate and set the calorie value.
     *
     * @return void
     */
    public function calculateCalories()
    {
        $protein_calories = $this->total_protein * 4;
        $carbs_calories = $this->total_karbohidrat * 4;
        $fat_calories = $this->total_lemak * 9;

        $this->kalori = $protein_calories + $carbs_calories + $fat_calories;
    }

}
