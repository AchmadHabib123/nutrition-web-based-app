<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StandarDiit extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'min_kalori',
        'max_kalori',
        'target_protein_ratio',
        'target_karbohidrat_ratio',
        'target_lemak_ratio',
        'kategori_bahan_terlarang',
        'bahan_makanan_terlarang_ids',
        'pola_porsi_kategori',
        'catatan',
    ];

    protected $casts = [
        'kategori_bahan_terlarang' => 'array',
        'bahan_makanan_terlarang_ids' => 'array',
        'pola_porsi_kategori' => 'array',
    ];

    /**
     * Relasi ke Patient (Pasien yang menerapkan standar diit ini).
     */
    public function patients()
    {
        return $this->hasMany(Patient::class);
    }
}
