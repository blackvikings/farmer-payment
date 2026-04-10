<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Organizer extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'contact_info'];

    public function farmers()
    {
        return $this->hasMany(Farmer::class);
    }
}
