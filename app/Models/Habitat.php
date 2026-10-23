<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Habitat extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'habitats';
    protected $fillable = ['name', 'type', 'capacity', 'status'];
    protected $hidden = ['created_at', 'updated_at'];

    public function getRouteKeyName(): string
    {
        return 'id';
    }

    public function animals()
    {
        return $this->hasMany(Animal::class, 'habitat_id');
    }
}
