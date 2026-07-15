<?php 
// app/Infrastructure/Persistence/Models/User.php

namespace App\Infrastructure\Persistence\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;

    protected $fillable = [
        'name', 'email', 'username', 'phone', 'password'
    ];

    protected $hidden = ['password'];
}