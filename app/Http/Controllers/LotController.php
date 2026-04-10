<?php

namespace App\Http\Controllers;

use App\Models\Lot;
use App\Models\Agreement;
use App\Models\Organizer;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

/**
 * Class LotController
 * @package App\Http\Controllers
 *
 * This controller is responsible for handling all operations related to production lots.
 * It manages the creation of new lots, validates them against business rules (FR2),
 * and displays them in the user interface.
 */
class LotController extends Controller
{
    /**
     * Displays a list of all lots.
     * This method retrieves all lot records from the database, eager-loading the related
     * agreement and farmer data to prevent N+1 query problems. It then passes the
     * collected data to the `lots.index` view.
     *
     * @return \Illuminate\View\View
     */
    public function indexLots()
    {
        // Retrieve all lots with their associated agreement and farmer.
        $lots = Lot::with('agreement.farmer')->get();
        // Return the main lot listing view, passing the retrieved lots.
        return view('lots.index', compact('lots'));
    }

    /**
     * Shows the form for creating a new lot.
     * This method prepares the necessary data (all agreements and organizers) for the
     * lot creation form. This data is used to populate dropdowns from which the user
     * can select the relevant farmer agreement and organizer.
     *
     * @return \Illuminate\View\View
     */
    public function createLot()
    {
        // Fetch all agreements and organizers to be used in the form's dropdowns.
        $agreements = Agreement::with('farmer')->get();
        $organizers = Organizer::all();
        // Return the lot creation view, passing the agreements and organizers.
        return view('lots.create', compact('agreements', 'organizers'));
    }

    /**
     * Validates and stores a new lot from a web form submission.
     * This is the core method for FR2 (Agreement Validation). It performs a series of checks:
     * 1. Validates the incoming request data (e.g., required fields, uniqueness).
     * 2. Checks if the selected farmer agreement is currently active.
     * 3. Verifies that the farmer is correctly mapped to the selected organizer.
     *
     * If any validation fails, the lot is saved with a 'rejected' status and the user is
     * redirected back with an error. If all checks pass, the lot is saved as 'accepted'.
     *
     * @param Request $request The incoming HTTP request from the creation form.
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeWeb(Request $request)
    {
        try {
            // Step 1: Validate the basic structure and data types of the incoming request.
            $validated = $request->validate([
                'agreement_id' => 'required|exists:agreements,id',
                'organizer_id' => 'required|exists:organizers,id',
                'lot_number' => 'required|string|unique:lots,lot_number',
                'quantity' => 'required|numeric|min:0.01',
            ]);

            // Step 2: Fetch the full Agreement record, including the associated Farmer.
            $agreement = Agreement::with('farmer')->findOrFail($validated['agreement_id']);

            // Step 3: (FR2 Validation) Check if the agreement is currently active.
            $currentDate = Carbon::now();
            if ($currentDate->lt($agreement->start_date) || $currentDate->gt($agreement->end_date)) {
                // If the agreement is not active, create a rejected lot record for audit purposes.
                $this->rejectLot($validated, 'Agreement is not active or has expired.');
                // Redirect back with an error message.
                return redirect()->back()->with('error', 'Lot validation failed.')->with('reason', 'Agreement is not active or has expired.')->withInput();
            }

            // Step 4: (FR2 Validation) Check if the farmer belongs to the selected organizer.
            if ($agreement->farmer->organizer_id != $validated['organizer_id']) {
                // If the mapping is incorrect, create a rejected lot record.
                $this->rejectLot($validated, 'Invalid Farmer-Organizer mapping for this agreement.');
                // Redirect back with an error message.
                return redirect()->back()->with('error', 'Lot validation failed.')->with('reason', 'Invalid Farmer-Organizer mapping.')->withInput();
            }

            // Step 5: If all business rule validations pass, create the lot with an 'accepted' status.
            Lot::create([
                'agreement_id' => $validated['agreement_id'],
                'lot_number' => $validated['lot_number'],
                'quantity' => $validated['quantity'],
                'status' => 'accepted',
            ]);

            // Redirect to the lot index page with a success message.
            return redirect()->route('lots.index')->with('success', 'Lot received and accepted successfully.');

        } catch (ValidationException $e) {
            // Catch any standard Laravel validation errors and redirect back with the errors.
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            // Catch any other unexpected errors and redirect back with a generic error message.
            return redirect()->back()->with('error', 'An unexpected error occurred: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * A private helper method to create a rejected lot record.
     * This method is called when a business rule validation fails in the `storeWeb` method.
     * It creates a new Lot entry with a 'rejected' status and stores the reason for the
     * rejection, providing a clear audit trail for failed submissions.
     *
     * @param array $data The validated data from the request.
     * @param string $reason The specific reason for the rejection.
     * @return void
     */
    private function rejectLot(array $data, string $reason): void
    {
        // Create a new lot record with the 'rejected' status and the reason.
        Lot::create([
            'agreement_id' => $data['agreement_id'],
            'lot_number' => $data['lot_number'],
            'quantity' => $data['quantity'],
            'status' => 'rejected',
            'rejection_reason' => $reason
        ]);
    }
}
