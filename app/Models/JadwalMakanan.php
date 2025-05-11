<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JadwalMakanan extends Model
{
    use HasFactory;
    protected $fillable = [
        'tanggal_mulai',
        'tanggal_selesai',
        'tipe_pasien',
        'keterangan',
    ];
    public function menus()
    {
        return $this->belongsToMany(Menu::class, 'jadwal_makanan_menu', 'jadwal_makanan_id', 'menu_id')
                    ->withPivot(['tanggal', 'waktu_makan'])
                    ->withTimestamps();
    }

}
