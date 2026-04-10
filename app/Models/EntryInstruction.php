<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EntryInstruction extends Model
{
    use HasFactory;

    protected $fillable = [
        'lot_number',
        'status',
    ];

    public function frn()
    {
        return $this->belongsTo(Frn::class, 'lot_number', 'lot_number');
    }
}
