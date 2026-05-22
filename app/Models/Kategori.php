<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kategori extends Model
{
    use HasFactory;

    // KUNCI UTAMA: Beritahu Laravel untuk membaca tabel 'kategoris', bukan 'categories'
    protected $table = 'kategoris';

    // Opsional: Jika Anda menggunakan mass assignment di Controller
    protected $guarded = []; 

    // Relasi ke model Barang (Satu kategori memiliki banyak barang)
    public function barangs()
    {
        return $this->hasMany(Barang::class, 'kategori_id');
    }
}