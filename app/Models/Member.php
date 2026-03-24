<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
// use Illuminate\Database\Eloquent\Model;

class Member extends Authenticatable
{
    protected $fillable = ['name', 'no_hp', 'nisn', 'password'];
}