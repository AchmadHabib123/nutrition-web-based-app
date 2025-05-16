<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
        'status_pasien' => 'aktif',

    ];    

    // Jika ada relasi dengan makanan yang dikonsumsi
    public function foodConsumptions()
    {
        return $this->hasMany(FoodConsumption::class);
    }
    // public function menu()
    // {
    //     return $this->belongsTo(Menu::class, 'tipe_pasien', 'tipe');
    // }
}
