<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParameterStandard extends Model
{
    use HasFactory;

    protected $fillable = [
        'parameter_id',
        'min_accepted',
        'max_accepted',
        'min_conditional',
        'max_conditional'
    ];

    public function parameter()
    {
        return $this->belongsTo(Parameter::class);
    }
}
