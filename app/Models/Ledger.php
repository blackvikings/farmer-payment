<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ledger extends Model
{
    use HasFactory;

    protected $fillable = [
        'entity_type',
        'entity_id',
        'transaction_type',
        'amount',
        'description',
        'lot_id',
    ];

    /**
     * Get the entity (Farmer or Organizer) associated with the ledger entry.
     */
    public function entity()
    {
        return $this->morphTo();
    }
}
