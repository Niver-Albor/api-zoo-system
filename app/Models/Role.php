<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;
    
    protected $table = 'roles';
    protected $fillable = ['name', 'description'];
    protected $hidden = ['created_at', 'updated_at'];

    public function permissions()
    {
        return $this->belongsToMany(
            Permission::class,
            'permissions_roles',
            'role_id',
            'permission_id'
            );
    }
}
