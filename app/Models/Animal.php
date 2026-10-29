<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class Animal extends Model implements Auditable
{
    use HasFactory, SoftDeletes, \OwenIt\Auditing\Auditable;

    protected $table = 'animals';
    protected $fillable = [
        'habitat_id', 
        'species_id', 
        'code', 
        'name', 
        'sex', 
        'birth_date', 
        'admission_date', 
        'health_status'
        ];
    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];

    public function habitat()
    {
        return $this->belongsTo(Habitat::class);
    }

    public function species()
    {
        return $this->belongsTo(Species::class);
    }

    public function getRouteKeyName(): string
    {
        return 'code';
    }
}
