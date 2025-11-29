<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Career extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'position',
        'description',
        'requirements',
        'benefits',
        'department',
        'job_type',
        'location',
        'is_active',
        'deadline',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'deadline' => 'datetime',
    ];

    public function applications()
    {
        return $this->hasMany(Application::class);
    }
}
