<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lot extends Model
{
    use HasFactory;

    protected $fillable = [
        'agreement_id',
        'lot_number',
        'quantity',
        'status',
        'rejection_reason',
        'qc_status',
        'payment_blocked',
        'final_quantity',
        'process_loss',
        'debit_note_id',
        'debit_override',
        'base_amount',
        'quality_deduction',
        'bonus_amount',
        'compensation_amount',
        'debit_recovery',
        'gross_payable',
        'net_payable',
        'payment_status',
        'pricing_approved',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'payment_blocked' => 'boolean',
        'pricing_approved' => 'boolean',
        'approved_at' => 'datetime',
        'quantity' => 'decimal:2',
        'final_quantity' => 'decimal:2',
        'process_loss' => 'decimal:2',
        'base_amount' => 'decimal:2',
        'quality_deduction' => 'decimal:2',
        'bonus_amount' => 'decimal:2',
        'compensation_amount' => 'decimal:2',
        'debit_recovery' => 'decimal:2',
        'gross_payable' => 'decimal:2',
        'net_payable' => 'decimal:2',
    ];

    public function agreement()
    {
        return $this->belongsTo(Agreement::class);
    }

    /**
     * Get the FRN associated with the Lot.
     * A Lot has one FRN.
     */
    public function frn()
    {
        return $this->hasOne(Frn::class, 'lot_number', 'lot_number');
    }
}
