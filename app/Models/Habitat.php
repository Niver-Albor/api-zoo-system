<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Habitat extends Model
{
    use HasFactory;

    protected $table = 'habitats';
    protected $fillable = ['name', 'type', 'capacity', 'status'];
    protected $hidden = ['created_at', 'updated_at'];
}
