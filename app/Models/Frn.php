<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Frn extends Model
{
    use HasFactory;

    // Use Lot No as Primary transaction key
    protected $primaryKey = 'lot_number';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'lot_number',
        'frn_number',
        'arrival_date',
        'gross_weight',
        'vehicle_number'
    ];

    public function lot()
    {
        return $this->belongsTo(Lot::class, 'lot_number', 'lot_number');
    }
}
