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
        'waktu_makan',
        'status',
        'menu_id',
        'tanggal',
        'actual_kalori',     
        'actual_protein',      
        'actual_karbohidrat',  
        'actual_lemak',        
        'consumption_percentage',
        'notes',
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
        return $this->belongsTo(Menu::class, 'menu_id'); // <--- KOREKSI INI
    }
}
