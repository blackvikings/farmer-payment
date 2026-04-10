<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AgreementParameter extends Model
{
    use HasFactory;

    protected $fillable = [
        'agreement_id',
        'name',
        'value',
    ];

    public function agreement()
    {
        return $this->belongsTo(Agreement::class);
    }
}
