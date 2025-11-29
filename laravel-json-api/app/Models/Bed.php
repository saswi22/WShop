<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bed extends Model
{
    use HasFactory;

    protected $fillable = [
        'facility_id', 'name', 'class', 'quantity', 'image', 'is_active'
    ];

    public function facility()
    {
        return $this->belongsTo(Facility::class);
    }
}
