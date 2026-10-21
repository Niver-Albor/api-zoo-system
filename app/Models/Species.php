<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Species extends Model
{
    use HasFactory;

    protected $table = 'species';
    protected $fillable = ['common_name', 'scientific_name', 'description'];
    protected $hidden = ['created_at', 'updated_at'];

    public function getRouteKeyName(): string
    {
        return 'id';
    }

    public function animals()
    {
        return $this->belongsTo(Animal::class, 'species_id');
    }
}
