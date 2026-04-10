<?php

namespace App\Http\Controllers;

use App\Models\Frn;
use App\Models\Lot;
use App\Models\DebitNote;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

/**
 * Class FrnController
 * @package App\Http\Controllers
 *
 * This controller is responsible for handling all operations related to Farmer Receipt Notes (FRNs).
 * FRNs are critical documents that signify the official arrival of a farmer's lot at the processing facility.
 * This controller ensures that FRNs are only created for valid, accepted lots and that all data is captured accurately.
 */
class FrnController extends Controller
{
    /**
     * Display a listing of all Farmer Receipt Notes.
     *
     * This method retrieves all FRN records from the database, eager-loading the associated Lot data
     * to provide comprehensive details on the list view. It's the main dashboard for viewing all processed arrivals.
     *
     * @return \Illuminate\View\View Returns a view populated with a collection of all FRNs.
     */
    public function indexFrns()
    {
        $frns = Frn::with('lot')->get();
        return view('frns.index', compact('frns'));
    }

    /**
     * Show the form for creating a new Farmer Receipt Note.
     *
     * This method prepares the FRN creation form. It intelligently filters the list of lots
     * to only include those that have a status of 'accepted' and do not already have an FRN,
     * preventing duplicate entries and ensuring business rule compliance.
     *
     * @return \Illuminate\View\View Returns the FRN creation view, passing a list of eligible lots.
     */
    public function createFrn()
    {
        $lots = Lot::where('status', 'accepted')->doesntHave('frn')->get();
        return view('frns.create', compact('lots'));
    }

    /**
     * Store a newly created Farmer Receipt Note in the database.
     *
     * This method is the core of FRN creation. It validates the incoming request data to ensure
     * that the FRN and Lot numbers are unique and that all required fields are present. It also
     * re-verifies that the lot has been accepted before creating the FRN record.
     *
     * @param Request $request The incoming HTTP request containing the FRN data.
     * @return RedirectResponse Redirects to the FRN index with a success or error message.
     */
    public function storeWeb(Request $request)
    {
        try {
            // FR3 Validation: Ensure all required fields are present and unique where necessary.
            $validated = $request->validate([
                'lot_number' => 'required|string|exists:lots,lot_number|unique:frns,lot_number',
                'frn_number' => 'required|string|unique:frns,frn_number',
                'arrival_date' => 'required|date',
                'gross_weight' => 'required|numeric|min:0.01',
                'vehicle_number' => 'nullable|string',
            ]);

            // Re-verify that the lot is still in an 'accepted' state before creating the FRN.
            $lot = Lot::where('lot_number', $validated['lot_number'])->first();
            if ($lot->status !== 'accepted') {
                return redirect()->back()->with('error', 'Cannot create FRN for a lot that is not accepted.')->withInput();
            }

            // Create the FRN record. The associated observer will handle automated post-creation tasks.
            Frn::create($validated);

            return redirect()->route('frns.index')->with('success', 'FRN captured successfully. Entry Instruction auto-created.');

        } catch (ValidationException $e) {
            // If validation fails, redirect back with the errors and original input.
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            // Catch any other unexpected errors and return a generic error message.
            return redirect()->back()->with('error', 'An unexpected error occurred: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Approve a specific Debit Note.
     *
     * This method handles the business logic for approving a debit note that may have been
     * automatically generated due to process loss. Approving it confirms that the deduction is valid.
     *
     * @param DebitNote $debitNote The DebitNote instance to be approved, resolved via route model binding.
     * @return RedirectResponse Redirects to the previous page with a success message.
     */
    public function approveDebitNote(DebitNote $debitNote)
    {
        $debitNote->update(['is_approved' => true]);
        return redirect()->back()->with('success', 'Debit Note Approved Successfully!');
    }
}
