<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rate extends Model
{
    use HasFactory;

    protected $fillable = [
        'parameter_id',
        'base_price',
        'version',
        'is_active'
    ];

    public function parameter()
    {
        return $this->belongsTo(Parameter::class);
    }
}
