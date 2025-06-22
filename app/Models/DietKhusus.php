<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DietKhusus extends Model
{
    use HasFactory;
    protected $table = 'diet_khusus';

    protected $fillable = [
        'nama',
        'deskripsi',
    ];

    /**
     * Relasi ke Menu (Menu yang kompatibel dengan diet khusus ini).
     */
    public function menus()
    {
        return $this->belongsToMany(Menu::class, 'menu_diet_khusus', 'diet_khusus_id', 'menu_id');
    }

    /**
     * Relasi ke Patient (Pasien yang menerapkan diet khusus ini).
     */
    public function patients()
    {
        return $this->belongsToMany(Patient::class, 'patient_diet_khusus', 'diet_khusus_id', 'patient_id');
    }
}
