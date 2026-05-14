<?php

namespace App\Http\Controllers;

use App\Models\DebitNote;
use App\Models\Ledger;
use App\Models\Lot;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * Class PricingController
 */
class PricingController extends Controller
{
    /**
     * Calculate the detailed pricing for a given lot.
     *
     * This is the core of the pricing engine. It performs a sequence of calculations:
     * 1.  **Base Amount**: Calculated from the lot's final quantity and the agreement's rate.
     * 2.  **Quality Deduction**: Applies deductions based on the lot's QC status and agreement parameters.
     * 3.  **Bonus Input**: Adds any applicable bonuses based on the agreement's bonus.
     * 4.  **Debit Recovery**: Recovers any approved debit notes associated with the lot (e.g., for process loss).
     * 5.  **Loss Rule Application**: Applies any loss rules from the agreement.
     * 6.  **Ledger Adjustments**: Considers the farmer's previous balance (advances or dues) for final adjustment.
     *
     * The final calculated values are then saved to the lot record.
     *
     * @param  Lot  $lot  The lot for which to calculate pricing.
     * @param  Request  $request  The incoming request, which may contain manual inputs like compensation.
     * @return RedirectResponse Redirects back with a success or error message.
     */
    public function calculatePricing(Lot $lot, Request $request)
    {
        if ($lot->status !== 'accepted') {
            return redirect()->back()->with('error', 'Only accepted lots can be priced.');
        }

        if ($lot->payment_blocked || $lot->qc_status === 'Rejected') {
            return redirect()->back()->with('error', 'Payment is blocked for this lot.');
        }

        if (! in_array($lot->qc_status, ['Accepted', 'Conditional'], true)) {
            return redirect()->back()->with('error', 'Quality validation must be completed before pricing.');
        }

        // Prevent recalculation if the pricing has already been approved and locked.
        if ($lot->pricing_approved) {
            return redirect()->back()->with('error', 'Pricing is already approved and locked for this lot.');
        }

        $validated = $request->validate([
            'compensation_amount' => 'nullable|numeric|min:0',
        ]);

        $agreement = $lot->agreement()->with(['lossRules', 'parameters'])->first();

        // --- 1. Base Amount Calculation ---
        $quantity = (float) ($lot->final_quantity ?? $lot->quantity);
        $basePrice = (float) ($agreement->rate ?? 0);
        $baseAmount = $quantity * $basePrice;

        // --- 2. Quality Deduction (based on parameters) ---
        $qualityDeduction = 0;
        if ($lot->qc_status === 'Conditional') {
            $qualityDeduction = $baseAmount * 0.02;
        }

        // --- 3. Bonus & Compensation Input ---
        $bonusAmount = (float) ($agreement->bonus ?? 0);
        $compensationAmount = (float) ($validated['compensation_amount'] ?? 0);

        // --- 4. Debit Recovery ---
        $debitRecovery = 0;
        if ($lot->debit_note_id) {
            $debitNote = DebitNote::find($lot->debit_note_id);
            if ($debitNote && $debitNote->is_approved) {
                $debitRecovery = $debitNote->amount;
            }
        }

        // --- 5. Loss Rule Application ---
        $lossDeduction = 0;
        foreach ($agreement->lossRules as $rule) {
            // Placeholder logic for loss rule application
            // Example: if loss rule is "weight_loss" and value is "5", deduct 5%
            if (strpos(strtolower($rule->name), 'weight') !== false) {
                $lossDeduction += $baseAmount * ((float) $rule->value / 100);
            }
        }

        // --- 6. Ledger Adjustments & Final Calculation ---
        $grossPayable = $baseAmount + $bonusAmount + $compensationAmount;

        $farmerId = $agreement->farmer_id;
        $previousBalance = Ledger::where('entity_type', 'farmer')->where('entity_id', $farmerId)->sum('amount');
        $advanceRecovery = $previousBalance < 0 ? abs($previousBalance) : 0;

        $netPayable = $grossPayable - $qualityDeduction - $lossDeduction - $debitRecovery - $advanceRecovery;

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

        return redirect()->back()->with('success', 'Pricing calculated successfully for Lot: '.$lot->lot_number);
    }

    /**
     * Approve the pricing for a lot.
     *
     * This method provides a simple, single-level approval mechanism. Once approved, the pricing
     * data for the lot is locked, preventing any further recalculations. This is a critical step
     * before payment can be processed.
     *
     * @param  Lot  $lot  The lot whose pricing is to be approved.
     * @return RedirectResponse
     */
    public function approvePricing(Lot $lot)
    {
        if ($lot->status !== 'accepted' || $lot->payment_blocked || $lot->qc_status === 'Rejected') {
            return redirect()->back()->with('error', 'Cannot approve pricing for this lot.');
        }

        if (is_null($lot->net_payable)) {
            return redirect()->back()->with('error', 'Calculate pricing before approval.');
        }

        // Lock the pricing data by setting the approval flag and recording the user and timestamp.
        $lot->update([
            'pricing_approved' => true,
            'approved_by' => Auth::check() ? Auth::id() : null, // Use authenticated user's ID, or null if not logged in.
            'approved_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Pricing approved and locked for Lot: '.$lot->lot_number);
    }

    /**
     * Process the final payment for a lot and update the ledgers.
     *
     * This method executes the final payment transaction. It ensures that payment is only processed
     * for lots with approved pricing. It updates the lot's payment status and creates a corresponding
     * entry in the farmer's ledger to reflect the payment.
     *
     * @param  Lot  $lot  The lot for which to process payment.
     * @return RedirectResponse
     */
    public function processPayment(Lot $lot)
    {
        // Ensure pricing is approved and payment has not already been made.
        if (! $lot->pricing_approved) {
            return redirect()->back()->with('error', 'Cannot process payment until pricing is approved.');
        }
        if ($lot->status !== 'accepted' || $lot->payment_blocked || $lot->qc_status === 'Rejected') {
            return redirect()->back()->with('error', 'Cannot process payment for this lot.');
        }
        if (is_null($lot->net_payable) || $lot->net_payable < 0) {
            return redirect()->back()->with('error', 'Cannot process payment without a valid payable amount.');
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
                'description' => 'Payment for Lot: '.$lot->lot_number,
                'lot_id' => $lot->id,
            ]);

            // Future enhancement: Organizer commission logic could be added here.
        });

        return redirect()->back()->with('success', 'Payment processed and ledgers updated for Lot: '.$lot->lot_number);
    }
}
