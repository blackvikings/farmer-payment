<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Farmer extends Model
{
    use HasFactory;

    protected $fillable = [
        'organizer_id',
        'name',
        'phone_number',
        'address'
    ];

    public function organizer()
    {
        return $this->belongsTo(Organizer::class);
    }
}
