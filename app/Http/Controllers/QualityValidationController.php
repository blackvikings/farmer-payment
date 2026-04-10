<?php

namespace App\Http\Controllers;

use App\Models\Lot;
use App\Models\QualityCheck;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Class QualityValidationController
 * @package App\Http\Controllers
 *
 * This controller is responsible for all logic related to FR-5 (Quality Validation).
 * It provides the functionality for quality inspectors to input observed values for various
 * parameters, compares them against predefined standards, and classifies the lot's overall
 * quality status. It also handles automatic payment blocking for rejected lots.
 */
class QualityValidationController extends Controller
{
    /**
     * Display a list of all lots that are awaiting quality validation.
     *
     * This method fetches all lots that have been 'accepted' during the initial reception
     * phase, as these are the only ones eligible for quality control.
     *
     * @return \Illuminate\View\View
     */
    public function indexQuality()
    {
        $lots = Lot::where('status', 'accepted')->with('agreement.farmer')->get();
        return view('quality.index', compact('lots'));
    }

    /**
     * Show the dynamic form for performing a quality check on a specific lot.
     *
     * This method retrieves the specified lot and its associated agreement parameters.
     * The parameters are passed to the view to dynamically generate the QC form.
     *
     * @param string $lotNumber The unique identifier for the lot to be checked.
     * @return \Illuminate\View\View
     */
    public function checkQuality($lotNumber)
    {
        $lot = Lot::where('lot_number', $lotNumber)->firstOrFail();
        $parameters = $lot->agreement->parameters;
        return view('quality.check', compact('lot', 'parameters'));
    }

    /**
     * Process and store the submitted quality check data for a lot.
     *
     * This is the core method for quality validation. It iterates through each submitted
     * parameter check, compares the observed value against the defined standards, and
     * determines if the check is 'Accepted', 'Conditional', or 'Rejected'.
     *
     * Based on the individual checks, it calculates an overall QC status for the lot.
     * If any check is 'Rejected', the entire lot is rejected, and payment is automatically blocked.
     *
     * @param Request $request The incoming HTTP request containing the QC data.
     * @param string $lotNumber The lot number being validated.
     * @return \Illuminate\Http\RedirectResponse
     */
    public function submitQualityChecksWeb(Request $request, $lotNumber)
    {
        try {
            $validated = $request->validate([
                'final_quantity' => 'required|numeric|min:0',
                'checks' => 'required|array',
                'checks.*.parameter_id' => 'required|exists:agreement_parameters,id', // Validate against agreement_parameters table
                'checks.*.observed_value' => 'required|numeric',
            ]);

            $lot = Lot::where('lot_number', $lotNumber)->firstOrFail();

            if ($lot->status !== 'accepted') {
                return redirect()->back()->with('error', 'Lot must be accepted before QC.')->withInput();
            }

            $overallStatus = 'Accepted'; // Start with the most optimistic status.

            DB::transaction(function () use ($lot, $validated, &$overallStatus) {
                foreach ($validated['checks'] as $checkData) {
                    // Fetch the specific AgreementParameter for this check
                    $agreementParameter = $lot->agreement->parameters()->findOrFail($checkData['parameter_id']);
                    $observedValue = $checkData['observed_value'];
                    $targetValue = (float) $agreementParameter->value; // Cast to float for numeric comparison

                    $checkStatus = 'Rejected'; // Default to rejected.

                    // Simple comparison logic based on a single target value
                    $tolerance = 0.05; // 5% tolerance for 'Conditional'
                    $minConditional = $targetValue * (1 - $tolerance);
                    $maxConditional = $targetValue * (1 + $tolerance);

                    if ($observedValue == $targetValue) {
                        $checkStatus = 'Accepted';
                    } elseif ($observedValue >= $minConditional && $observedValue <= $maxConditional) {
                        $checkStatus = 'Conditional';
                        if ($overallStatus !== 'Rejected') { // Don't downgrade from Rejected
                            $overallStatus = 'Conditional';
                        }
                    } else {
                        $overallStatus = 'Rejected';
                    }

                    // Create a record for each individual quality check.
                    QualityCheck::create([
                        'lot_number' => $lot->lot_number,
                        'parameter_id' => $agreementParameter->id, // Store the agreement_parameter_id
                        'observed_value' => $observedValue,
                        'status' => $checkStatus,
                    ]);
                }

                // Update the lot with the final QC status and payment block flag.
                $lot->qc_status = $overallStatus;
                $lot->payment_blocked = ($overallStatus === 'Rejected');
                $lot->final_quantity = $validated['final_quantity']; // Update final quantity
                $lot->save();
            });

            return redirect()->route('quality.index')->with('success', "QC for Lot #{$lotNumber} completed. Final Status: {$overallStatus}");

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage())->withInput();
        }
    }
}
