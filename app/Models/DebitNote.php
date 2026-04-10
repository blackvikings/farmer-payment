<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DebitNote extends Model
{
    use HasFactory;

    protected $fillable = [
        'lot_id',
        'amount',
        'reason',
        'is_approved'
    ];

    public function lot()
    {
        return $this->belongsTo(Lot::class);
    }
}
