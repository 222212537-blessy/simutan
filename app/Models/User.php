<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    // app/Models/User.php

    protected $fillable = ['id', 'name', 'panggilan', 'role', 'username', 'password', 'ttd', 'foto'];


    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Check if the user is an admin.
     *
     * @return bool
     */
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    /**
     * Check if the user is a supervisor.
     *
     * @return bool
     */
    public function isSupervisor()
    {
        return $this->role === 'supervisor';
    }

    /**
     * Check if the user is a pegawai.
     *
     * @return bool
     */
    public function isPegawai()
    {
        return $this->role === 'pegawai';
    }

    public function hasRole($role)
    {
        // Memeriksa apakah role pengguna sesuai dengan peran yang diberikan
        return $this->role === $role;
    }

    

}
