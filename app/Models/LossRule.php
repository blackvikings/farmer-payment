<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LossRule extends Model
{
    use HasFactory;

    protected $fillable = [
        'rule_name',
        'max_allowable_loss_percentage',
        'version',
        'is_active'
    ];
}
