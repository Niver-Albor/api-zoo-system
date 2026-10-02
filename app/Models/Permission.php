<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    use HasFactory;

    protected $table = 'permissions';
    protected $fillable = ['name', 'description'];
    protected $hidden = ['created_at', 'updated_at'];

    public function roles()
    {
        return $this->belongsToMany(
            Role::class,
            'permissions_roles',
            'permission_id',
            'role_id'
            );
    }
}
