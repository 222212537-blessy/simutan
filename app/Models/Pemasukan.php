<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pemasukan extends Model
{
    use HasFactory;
     // Tentukan kolom-kolom yang dapat diisi
     protected $fillable = [
        'id',
        'barang_id',
        'qty',
        'tanggal',
    ];
}
