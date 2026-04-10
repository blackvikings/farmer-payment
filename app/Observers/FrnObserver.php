<?php

namespace App\Observers;

use App\Models\Frn;
use App\Models\Lot;
use App\Models\LossRule;
use App\Models\DebitNote;

class FrnObserver
{
    /**
     * Handle the Frn "created" event.
     *
     * @param  \App\Models\Frn  $frn
     * @return void
     */
    public function created(Frn $frn)
    {
        $this->calculateProcessLoss($frn);
    }

    /**
     * Handle the Frn "updated" event.
     *
     * @param  \App\Models\Frn  $frn
     * @return void
     */
    public function updated(Frn $frn)
    {
        $this->calculateProcessLoss($frn);
    }

    /**
     * Calculate process loss and create a debit note if necessary.
     *
     * @param  \App\Models\Frn  $frn
     * @return void
     */
    protected function calculateProcessLoss(Frn $frn)
    {
        $lot = $frn->lot;

        // Ensure we have initial quantity and gross weight to calculate loss
        if (!$lot || is_null($lot->quantity) || is_null($frn->gross_weight)) {
            return;
        }

        // 1. Calculate Process Loss
        $initialQuantity = $lot->quantity;
        $finalQuantity = $frn->gross_weight;
        $loss = $initialQuantity - $finalQuantity;
        $lossPercentage = ($initialQuantity > 0) ? ($loss / $initialQuantity) * 100 : 0;

        // Update the lot with final quantity and process loss
        $lot->final_quantity = $finalQuantity;
        $lot->process_loss = $lossPercentage;

        // 2. Apply Loss Rule
        $activeRule = LossRule::where('is_active', true)->first();
        if ($activeRule && $lossPercentage > $activeRule->max_allowable_loss_percentage) {

            // 3. Auto-create Debit Note
            $debitNote = DebitNote::create([
                'lot_id' => $lot->id,
                'amount' => $loss - ($initialQuantity * $activeRule->max_allowable_loss_percentage / 100), // The excess loss amount
                'reason' => 'Process loss of ' . number_format($lossPercentage, 2) . '% exceeded the allowable limit of ' . $activeRule->max_allowable_loss_percentage . '%.',
                'is_approved' => false, // Default to not approved
            ]);

            $lot->debit_note_id = $debitNote->id;
        }

        $lot->save();
    }
}
