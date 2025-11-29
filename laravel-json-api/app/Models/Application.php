<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    protected $fillable = [
        'career_id',
        'position',
        'name',
        'email',
        'phone',
        'education',
        'cv_url',
        'cover_letter',
        'status'
    ];

    public function career()
    {
        return $this->belongsTo(Career::class);
    }
}
