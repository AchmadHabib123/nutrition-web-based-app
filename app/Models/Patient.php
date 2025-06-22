<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\FoodConsumption;

class Patient extends Model
{
    use HasFactory;

    protected $fillable = [
        'no_kamar',
        'nama_pasien',
        'riwayat_penyakit',
        'kalori_makanan',
        'kalori_harian',
        'berat_badan',
        'tinggi_badan',
        'usia',
        'jenis_kelamin',
        'tipe_pasien',
        'kondisi_diet_klinis',
        'kalori_harian',
        'status_pasien',
        'standar_diit_id',

    ];    

    // Jika ada relasi dengan makanan yang dikonsumsi
    public function foodConsumptions()
    {
        return $this->hasMany(FoodConsumption::class);
    }

    public function getTotalKaloriMakananTerkonsumsiAttribute()
    {
        // Pastikan Anda memuat relasi foodConsumptions dan menunya secara eager loading
        // di controller agar tidak terjadi N+1 problem.
        // Jika tidak di-eager load, ini akan menjalankan query terpisah setiap kali diakses.
        return $this->foodConsumptions
                    ->where('status', 'consumed')
                    ->sum(fn($foodConsumption) => $foodConsumption->menu ? $foodConsumption->menu->kalori : 0);
    }
    public function standarDiit()
    {
        return $this->belongsTo(StandarDiit::class);
    }

    /**
     * Relasi ke DietKhusus (untuk BAB 3 - jika banyak diet khusus per pasien).
     * Mengasumsikan pasien dapat memiliki banyak diet khusus.
     */
    public function dietKhusus()
    {
        return $this->belongsToMany(DietKhusus::class, 'patient_diet_khusus', 'patient_id', 'diet_khusus_id');
    }
}
