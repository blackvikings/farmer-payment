<?php

namespace App\Http\Controllers;

use App\Models\Agreement;
use App\Models\Farmer;
use App\Models\Organizer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MasterManagementController extends Controller
{
    // ... (Organizer and Farmer methods)
    /**
     * Displays a list of all organizers.
     *
     * @return View
     */
    public function indexOrganizers()
    {
        $organizers = Organizer::all();

        return view('organizers.index', compact('organizers'));
    }

    /**
     * Shows the form for creating a new organizer.
     *
     * @return View
     */
    public function createOrganizer()
    {
        return view('organizers.create');
    }

    /**
     * Stores a newly created organizer in the database.
     *
     * @return RedirectResponse
     */
    public function storeOrganizer(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:organizers,name',
            'contact_info' => 'nullable|string',
        ]);
        Organizer::create($validated);

        return redirect()->route('organizers.index')->with('success', 'Organizer created successfully.');
    }

    // --- Farmer Management ---

    /**
     * Displays a list of all farmers, with their associated organizer.
     *
     * @return View
     */
    public function indexFarmers()
    {
        $farmers = Farmer::with('organizer')->get();

        return view('farmers.index', compact('farmers'));
    }

    /**
     * Shows the form for creating a new farmer.
     *
     * @return View
     */
    public function createFarmer()
    {
        $organizers = Organizer::all();

        return view('farmers.create', compact('organizers'));
    }

    /**
     * Stores a newly created farmer in the database.
     *
     * @return RedirectResponse
     */
    public function storeFarmer(Request $request)
    {
        $validated = $request->validate([
            'organizer_id' => 'required|exists:organizers,id',
            'name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:15|unique:farmers,phone_number',
            'address' => 'required|string',
        ]);
        Farmer::create($validated);

        return redirect()->route('farmers.index')->with('success', 'Farmer created successfully.');
    }
    // --- Agreement Management ---

    public function indexAgreements()
    {
        $agreements = Agreement::with(['farmer'])->get();

        return view('agreements.index', compact('agreements'));
    }

    public function createAgreement()
    {
        $farmers = Farmer::pluck('name', 'id');

        return view('agreements.create', compact('farmers'));
    }

    public function storeAgreement(Request $request)
    {
        $validated = $request->validate([
            'farmer_id' => 'required|exists:farmers,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'rate' => 'required|numeric|min:0',
            'bonus' => 'nullable|numeric|min:0',
            'loss_rules' => 'nullable|array',
            'loss_rules.*.name' => 'nullable|string|max:255',
            'loss_rules.*.value' => 'nullable|numeric|min:0',
            'parameters' => 'nullable|array',
            'parameters.*.name' => 'nullable|string|max:255',
            'parameters.*.value' => 'nullable|numeric',
        ]);

        $agreement = Agreement::create($validated);

        if (isset($validated['loss_rules'])) {
            foreach ($validated['loss_rules'] as $rule) {
                // Only create the rule if a name is provided
                if (! empty($rule['name'])) {
                    $agreement->lossRules()->create([
                        'name' => $rule['name'],
                        'value' => $rule['value'] ?? 0,
                    ]);
                }
            }
        }

        if (isset($validated['parameters'])) {
            foreach ($validated['parameters'] as $parameter) {
                // Only create the parameter if a name is provided
                if (! empty($parameter['name'])) {
                    $agreement->parameters()->create([
                        'name' => $parameter['name'],
                        'value' => $parameter['value'] ?? 0,
                    ]);
                }
            }
        }

        return redirect()->route('agreements.index')->with('success', 'Agreement created successfully.');
    }
}
