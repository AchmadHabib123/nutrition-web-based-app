<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SiklusMenu extends Model
{
    use HasFactory;

    protected $fillable = [
        'hari_siklus',
        'waktu_makan',
        'tipe_pasien',
        'menu_id',
    ];

    protected $casts = [
        'hari_siklus' => 'integer',
    ];

    /**
     * Relasi ke Menu yang disajikan dalam slot siklus ini.
     */
    public function menu()
    {
        return $this->belongsTo(Menu::class);
    }
}
