<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Satuan extends Model
{
    use HasFactory;

    protected $primaryKey = 'satuan_id'; // Set the primary key

    // Define the relationship with the Barang model
    public function barangs()
    {
        return $this->hasMany(Barang::class, 'satuan_id', 'satuan_id');
    }
}

