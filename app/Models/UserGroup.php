<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserGroup extends Model
{
    protected $table = 'user_groups';
    protected $fillable = ['name','slug','description'];

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'group_permission', 'group_id', 'permission_id');
    }
}
