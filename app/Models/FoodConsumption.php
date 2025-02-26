<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FoodConsumption extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'nama_makanan',
        'kalori',
    ];

    /**
     * Relasi ke Patient.
     */
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    /**
     * Relasi ke Menu (Opsional).
     */
    public function menu()
    {
        return $this->belongsTo(Menu::class, 'nama_makanan', 'nama_makanan');
    }
}
