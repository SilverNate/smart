<?php

namespace App\Models;

use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
  use HasApiTokens, Notifiable, SoftDeletes;

  protected $fillable = [
      'username', 'email', 'password', 'role','role_id', 'school_name','school_id',
  ];
  protected $hidden = [
       'password', 'remember_token',
  ];
}
