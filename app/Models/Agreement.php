<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Agreement extends Model
{
    use HasFactory;

    protected $fillable = [
        'farmer_id',
        'start_date',
        'end_date',
        'rate', // New field
        'bonus', // New field
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function farmer()
    {
        return $this->belongsTo(Farmer::class);
    }

    public function lossRules()
    {
        return $this->hasMany(AgreementLossRule::class);
    }

    public function parameters()
    {
        return $this->hasMany(AgreementParameter::class);
    }
}
