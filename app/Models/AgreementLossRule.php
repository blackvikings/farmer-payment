<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AgreementLossRule extends Model
{
    use HasFactory;

    protected $fillable = [
        'agreement_id',
        'name',
        'value',
    ];

    protected $casts = [
        'value' => 'decimal:2',
    ];

    public function agreement()
    {
        return $this->belongsTo(Agreement::class);
    }
}
