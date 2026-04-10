<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EntryInstruction extends Model
{
    use HasFactory;

    protected $fillable = [
        'frn_id',
        'instruction_type',
        'status',
        'details',
    ];

    public function frn()
    {
        return $this->belongsTo(Frn::class);
    }
}
