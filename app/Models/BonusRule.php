<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BonusRule extends Model
{
    use HasFactory;

    protected $fillable = [
        'rule_name',
        'condition',
        'bonus_amount',
        'version',
        'is_active'
    ];
}
