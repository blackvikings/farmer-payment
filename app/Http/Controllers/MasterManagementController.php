<?php

namespace App\Http\Controllers;

use App\Models\Farmer;
use App\Models\Organizer;
use App\Models\Agreement;
use App\Models\Rate;
use App\Models\BonusRule;
use App\Models\LossRule;
use App\Models\Parameter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Class MasterManagementController
 * @package App\Http\Controllers
 *
 * This controller acts as a central hub for managing all the master data of the application.
 * Master data includes foundational records that other parts of the system rely on, such as
 * farmers, organizers, agreements, and various business rules like rates and bonuses.
 * This controller provides methods for all CRUD (Create, Read, Update, Delete) operations
 * for each of these master data types, primarily for web-based administration.
 */
class MasterManagementController extends Controller
{
    // --- Organizer Management ---

    /**
     * Displays a list of all organizers.
     * @return \Illuminate\View\View
     */
    public function indexOrganizers()
    {
        $organizers = Organizer::all();
        return view('organizers.index', compact('organizers'));
    }

    /**
     * Shows the form for creating a new organizer.
     * @return \Illuminate\View\View
     */
    public function createOrganizer()
    {
        return view('organizers.create');
    }

    /**
     * Stores a newly created organizer in the database.
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
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
     * @return \Illuminate\View\View
     */
    public function indexFarmers()
    {
        $farmers = Farmer::with('organizer')->get();
        return view('farmers.index', compact('farmers'));
    }

    /**
     * Shows the form for creating a new farmer.
     * @return \Illuminate\View\View
     */
    public function createFarmer()
    {
        $organizers = Organizer::all();
        return view('farmers.create', compact('organizers'));
    }

    /**
     * Stores a newly created farmer in the database.
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
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

    /**
     * Displays a list of all agreements.
     * @return \Illuminate\View\View
     */
    public function indexAgreements()
    {
        $agreements = Agreement::with('farmer')->get();
        return view('agreements.index', compact('agreements'));
    }

    /**
     * Shows the form for creating a new agreement.
     * @return \Illuminate\View\View
     */
    public function createAgreement()
    {
        $farmers = Farmer::all();
        return view('agreements.create', compact('farmers'));
    }

    /**
     * Stores a newly created agreement in the database.
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeAgreement(Request $request)
    {
        $validated = $request->validate([
            'farmer_id' => 'required|exists:farmers,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'terms' => 'nullable|string',
        ]);
        Agreement::create($validated);
        return redirect()->route('agreements.index')->with('success', 'Agreement created successfully.');
    }

    // --- Parameter Management ---

    /**
     * Displays a list of all parameters.
     * @return \Illuminate\View\View
     */
    public function indexParameters()
    {
        $parameters = Parameter::all();
        return view('parameters.index', compact('parameters'));
    }

    /**
     * Shows the form for creating a new parameter.
     * @return \Illuminate\View\View
     */
    public function createParameter()
    {
        return view('parameters.create');
    }

    /**
     * Stores a newly created parameter in the database.
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeParameter(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:parameters,name',
            'type' => 'required|string|in:quality,production',
        ]);
        Parameter::create($validated);
        return redirect()->route('parameters.index')->with('success', 'Parameter created successfully.');
    }

    // --- Rates Management ---

    /**
     * Displays a list of all rates.
     * @return \Illuminate\View\View
     */
    public function indexRates()
    {
        $rates = Rate::with('parameter')->get();
        return view('rates.index', compact('rates'));
    }

    /**
     * Shows the form for creating a new rate.
     * @return \Illuminate\View\View
     */
    public function createRate()
    {
        $parameters = Parameter::all();
        return view('rates.create', compact('parameters'));
    }

    /**
     * Stores a new rate in the database.
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeRateWeb(Request $request)
    {
        $validated = $request->validate([
            'parameter_id' => 'required|exists:parameters,id',
            'base_price' => 'required|numeric|min:0',
        ]);
        Rate::create(array_merge($validated, ['version' => 1, 'is_active' => true]));
        return redirect()->route('rates.index')->with('success', 'Rate created successfully.');
    }

    /**
     * Shows the form for editing a rate.
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function editRate($id)
    {
        $rate = Rate::findOrFail($id);
        return view('rates.edit', compact('rate'));
    }

    /**
     * Updates a rate by creating a new version.
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateRateVersionWeb(Request $request, $id)
    {
        $validated = $request->validate(['base_price' => 'required|numeric|min:0']);
        $oldRate = Rate::findOrFail($id);

        DB::transaction(function () use ($oldRate, $validated) {
            $oldRate->update(['is_active' => false]);
            Rate::create([
                'parameter_id' => $oldRate->parameter_id,
                'base_price' => $validated['base_price'],
                'version' => $oldRate->version + 1,
                'is_active' => true,
            ]);
        });
        return redirect()->route('rates.index')->with('success', 'Rate updated successfully.');
    }

    // --- Bonus Rules Management ---

    /**
     * Displays a list of all bonus rules.
     * @return \Illuminate\View\View
     */
    public function indexBonusRules()
    {
        $bonusRules = BonusRule::all();
        return view('bonus_rules.index', compact('bonusRules'));
    }

    /**
     * Shows the form for creating a new bonus rule.
     * @return \Illuminate\View\View
     */
    public function createBonusRule()
    {
        return view('bonus_rules.create');
    }

    /**
     * Stores a new bonus rule in the database.
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeBonusRuleWeb(Request $request)
    {
        $validated = $request->validate([
            'rule_name' => 'required|string|max:255',
            'condition' => 'required|string',
            'bonus_amount' => 'required|numeric|min:0',
        ]);
        BonusRule::create(array_merge($validated, ['version' => 1, 'is_active' => true]));
        return redirect()->route('bonus-rules.index')->with('success', 'Bonus Rule created successfully.');
    }

    // --- Loss Rules Management ---

    /**
     * Displays a list of all loss rules.
     * @return \Illuminate\View\View
     */
    public function indexLossRules()
    {
        $lossRules = LossRule::all();
        return view('loss_rules.index', compact('lossRules'));
    }

    /**
     * Shows the form for creating a new loss rule.
     * @return \Illuminate\View\View
     */
    public function createLossRule()
    {
        return view('loss_rules.create');
    }

    /**
     * Stores a new loss rule in the database.
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeLossRuleWeb(Request $request)
    {
        $validated = $request->validate([
            'rule_name' => 'required|string|max:255',
            'max_allowable_loss_percentage' => 'required|numeric|min:0|max:100',
        ]);
        LossRule::create(array_merge($validated, ['version' => 1, 'is_active' => true]));
        return redirect()->route('loss-rules.index')->with('success', 'Loss Rule created successfully.');
    }
}
