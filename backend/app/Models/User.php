<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
   use HasApiTokens, HasFactory, Notifiable;

   protected $fillable =[
    'name', 'email', 'password', 'role', 'no_hp', 'alamat', 'foto_profile'
   ];

   protected $hidden = [
        'password', 'remember_token',
   ];

   protected function casts(): array
   {
        return [
            'password' => 'hashed',
        ];
   } 

   public function peminjaman(): HasMany {
        return $this->hasMany (Peminjaman::class);
   }

   public function logAktivitas(): HasMany {
        return $this->hasMany(LogAktivitas::class);
   }
}