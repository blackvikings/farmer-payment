<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QualityCheck extends Model
{
    use HasFactory;

    protected $fillable = [
        'lot_number',
        'parameter_id',
        'observed_value',
        'status'
    ];

    public function lot()
    {
        return $this->belongsTo(Lot::class, 'lot_number', 'lot_number');
    }

    public function parameter()
    {
        return $this->belongsTo(Parameter::class);
    }
}
