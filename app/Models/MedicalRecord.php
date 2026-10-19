<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicalRecord extends Model
{
    use HasFactory;

    protected $table = 'medical_records';
    protected $fillable = [
        'animal_id', 
        'user_id', 
        'replaces_id',
        'uuid',
        'record_type', 
        'diagnosis', 
        'treatment', 
        'performed_at', 
        'next_checkup_at'
        ];
    protected $hidden = ['created_at', 'updated_at'];

    protected $casts = [
        'performed_at' => 'datetime',
        'next_checkup_at' => 'datetime',
    ];

    public function animal()
    {
        return $this->belongsTo(Animal::class, 'animal_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function replacesRecord()
    {
        return $this->belongsTo(MedicalRecord::class, 'replaces_id');
    }

    public function replacedBy()
    {
        return $this->hasOne(MedicalRecord::class, 'replaces_id');
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }
}
