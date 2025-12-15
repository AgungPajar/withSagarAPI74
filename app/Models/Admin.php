<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Admin extends Authenticatable
{
    use Notifiable;

    protected $guard = 'admin';

    protected $fillable = [
        'name', 'email', 'password',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    public function groups()
    {
        return $this->morphToMany(UserGroup::class, 'user', 'group_user', 'user_id', 'group_id')->wherePivot('user_type','admin');
    }
}
