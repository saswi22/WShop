<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Facility extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'class', 'capacity', 'image', 'facilities', 'description'
    ];

    protected $casts = [
        'capacity' => 'integer',
    ];

    public function beds()
    {
        return $this->hasMany(Bed::class);
    }
}
