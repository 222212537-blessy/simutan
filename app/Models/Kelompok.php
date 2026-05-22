<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kelompok extends Model
{
    use HasFactory;
    
    protected $guarded = [];

    // Relasi satu-ke-banyak dengan model Barang
    public function barangs()
    {
        return $this->hasMany(Barang::class, 'kelompok_id', 'id'); // 'kelompok_id' adalah foreign key di tabel 'barangs'
    }
}
