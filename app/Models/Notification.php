<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $guarded = [];

    // Notification.php
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function permintaan()
    {
        return $this->belongsTo(Permintaan::class, 'permintaan_id');
    }

}
