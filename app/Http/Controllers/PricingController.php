<?php

namespace App\Http\Controllers;

use App\Models\Lot;
use App\Models\Rate;
use App\Models\DebitNote;
use App\Models\BonusRule;
use App\Models\Ledger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

/**
 * Class PricingController
 * @package App\Http\Controllers
 *
 * This controller is dedicated to the financial heart of the application: the pricing engine.
 * It is responsible for calculating the final payable amount for each lot by orchestrating
 * a series of financial calculations, including base price, deductions, bonuses, and recoveries.
 * It also handles the approval workflow and the final payment processing.
 */
class PricingController extends Controller
{
    /**
     * Calculate the detailed pricing for a given lot.
     *
     * This is the core of the pricing engine. It performs a sequence of calculations:
     * 1.  **Base Amount**: Calculated from the lot's final quantity and the active base rate.
     * 2.  **Quality Deduction**: Applies deductions based on the lot's QC status (e.g., 'conditional').
     * 3.  **Bonus Input**: Adds any applicable bonuses based on active bonus rules.
     * 4.  **Debit Recovery**: Recovers any approved debit notes associated with the lot (e.g., for process loss).
     * 5.  **Ledger Adjustments**: Considers the farmer's previous balance (advances or dues) for final adjustment.
     *
     * The final calculated values are then saved to the lot record.
     *
     * @param Lot $lot The lot for which to calculate pricing.
     * @param Request $request The incoming request, which may contain manual inputs like compensation.
     * @return \Illuminate\Http\RedirectResponse Redirects back with a success or error message.
     */
    public function calculatePricing(Lot $lot, Request $request)
    {
        // Prevent recalculation if the pricing has already been approved and locked.
        if ($lot->pricing_approved) {
            return redirect()->back()->with('error', 'Pricing is already approved and locked for this lot.');
        }

        // --- 1. Base Amount Calculation ---
        $baseRate = Rate::where('is_active', true)->first();
        $quantity = $lot->final_quantity ?? $lot->quantity;
        $basePrice = $baseRate ? $baseRate->base_price : 0;
        $baseAmount = $quantity * $basePrice;

        // --- 2. Quality Deduction ---
        // This is placeholder logic. A real implementation would have a dedicated deduction rule engine.
        $qualityDeduction = 0;
        if ($lot->qc_status === 'conditional') {
             $qualityDeduction = $baseAmount * 0.05; // Example: 5% deduction
        }

        // --- 3. Bonus & Compensation Input ---
        $bonusAmount = 0;
        $activeBonus = BonusRule::where('is_active', true)->first();
        if ($activeBonus) {
            // This is a simplified bonus calculation. A full implementation would evaluate the rule's condition.
            $bonusAmount = $quantity * $activeBonus->bonus_amount;
        }
        $compensationAmount = $request->input('compensation_amount', 0);

        // --- 4. Debit Recovery ---
        $debitRecovery = 0;
        if ($lot->debit_note_id) {
            $debitNote = DebitNote::find($lot->debit_note_id);
            // Only recover the amount if the debit note has been approved.
            if ($debitNote && $debitNote->is_approved) {
                $debitRecovery = $debitNote->amount;
            }
        }

        // --- 5. Ledger Adjustments & Final Calculation ---
        $grossPayable = $baseAmount + $bonusAmount + $compensationAmount;

        $farmerId = $lot->agreement->farmer_id;
        $previousBalance = Ledger::where('entity_type', 'farmer')->where('entity_id', $farmerId)->sum('amount');
        $advanceRecovery = $previousBalance < 0 ? abs($previousBalance) : 0;

        $netPayable = $grossPayable - $qualityDeduction - $debitRecovery - $advanceRecovery;

        // --- Save all calculated values to the lot ---
        $lot->update([
            'base_amount' => $baseAmount,
            'quality_deduction' => $qualityDeduction,
            'bonus_amount' => $bonusAmount,
            'compensation_amount' => $compensationAmount,
            'debit_recovery' => $debitRecovery,
            'gross_payable' => $grossPayable,
            'net_payable' => $netPayable,
        ]);

        return redirect()->back()->with('success', 'Pricing calculated successfully for Lot: ' . $lot->lot_number);
    }

    /**
     * Approve the pricing for a lot.
     *
     * This method provides a simple, single-level approval mechanism. Once approved, the pricing
     * data for the lot is locked, preventing any further recalculations. This is a critical step
     * before payment can be processed.
     *
     * @param Lot $lot The lot whose pricing is to be approved.
     * @return \Illuminate\Http\RedirectResponse
     */
    public function approvePricing(Lot $lot)
    {
        // Lock the pricing data by setting the approval flag and recording the user and timestamp.
        $lot->update([
            'pricing_approved' => true,
            'approved_by' => Auth::id() ?? 1, // Use authenticated user or a fallback.
            'approved_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Pricing approved and locked for Lot: ' . $lot->lot_number);
    }

    /**
     * Process the final payment for a lot and update the ledgers.
     *
     * This method executes the final payment transaction. It ensures that payment is only processed
     * for lots with approved pricing. It updates the lot's payment status and creates a corresponding
     * entry in the farmer's ledger to reflect the payment.
     *
     * @param Lot $lot The lot for which to process payment.
     * @return \Illuminate\Http\RedirectResponse
     */
    public function processPayment(Lot $lot)
    {
        // Ensure pricing is approved and payment has not already been made.
        if (!$lot->pricing_approved) {
             return redirect()->back()->with('error', 'Cannot process payment until pricing is approved.');
        }
        if ($lot->payment_status === 'paid') {
             return redirect()->back()->with('error', 'Payment has already been processed for this lot.');
        }

        // Use a database transaction to ensure atomicity.
        DB::transaction(function () use ($lot) {
            $farmerId = $lot->agreement->farmer_id;

            // 1. Update the lot's payment status to 'paid'.
            $lot->update(['payment_status' => 'paid']);

            // 2. Create a new entry in the ledger to record the payment to the farmer.
            Ledger::create([
                'entity_type' => 'farmer',
                'entity_id' => $farmerId,
                'transaction_type' => 'payment',
                'amount' => $lot->net_payable,
                'description' => 'Payment for Lot: ' . $lot->lot_number,
                'lot_id' => $lot->id,
            ]);

            // Future enhancement: Organizer commission logic could be added here.
        });

        return redirect()->back()->with('success', 'Payment processed and ledgers updated for Lot: ' . $lot->lot_number);
    }
}
