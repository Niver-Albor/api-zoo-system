<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Animal extends Model
{
    use HasFactory;

    protected $table = 'animals';
    protected $fillable = ['habitat_id', 'species_id', 'code', 'name', 'sex', 'birth_date', 'admission_date', 'health_status'];
    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];

    public function habitat()
    {
        return $this->belongsTo(Habitat::class);
    }

    public function species()
    {
        return $this->belongsTo(Species::class);
    }
}
