<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MasterManagementController;
use App\Http\Controllers\QualityValidationController;
use App\Http\Controllers\LotController;
use App\Http\Controllers\FrnController;
use App\Http\Controllers\PricingController;

Route::get('/', function () {
    return view('home');
});

// FR1: Master Management
Route::get('/farmers', [MasterManagementController::class, 'indexFarmers'])->name('farmers.index');
Route::get('/farmers/create', [MasterManagementController::class, 'createFarmer'])->name('farmers.create');
Route::post('/farmers', [MasterManagementController::class, 'storeFarmer'])->name('farmers.store');


Route::get('/organizers', [MasterManagementController::class, 'indexOrganizers'])->name('organizers.index');
Route::get('/organizers/create', [MasterManagementController::class, 'createOrganizer'])->name('organizers.create');
Route::post('/organizers', [MasterManagementController::class, 'storeOrganizer'])->name('organizers.store');

//Agreements
Route::get('/agreements', [MasterManagementController::class, 'indexAgreements'])->name('agreements.index');
Route::get('/agreements/create', [MasterManagementController::class, 'createAgreement'])->name('agreements.create');
Route::post('/agreements', [MasterManagementController::class, 'storeAgreement'])->name('agreements.store');

//Parameters
Route::get('/parameters', [MasterManagementController::class, 'indexParameters'])->name('parameters.index');
Route::get('/parameters/create', [MasterManagementController::class, 'createParameter'])->name('parameters.create');
Route::post('/parameters', [MasterManagementController::class, 'storeParameter'])->name('parameters.store');

// Rates
Route::get('/rates', [MasterManagementController::class, 'indexRates'])->name('rates.index');
Route::get('/rates/create', [MasterManagementController::class, 'createRate'])->name('rates.create');
Route::post('/rates', [MasterManagementController::class, 'storeRateWeb'])->name('rates.store');
Route::get('/rates/{id}/edit', [MasterManagementController::class, 'editRate'])->name('rates.edit');
Route::post('/rates/{id}', [MasterManagementController::class, 'updateRateVersionWeb'])->name('rates.update');

// Bonus Rules
Route::get('/bonus-rules', [MasterManagementController::class, 'indexBonusRules'])->name('bonus-rules.index');
Route::get('/bonus-rules/create', [MasterManagementController::class, 'createBonusRule'])->name('bonus-rules.create');
Route::post('/bonus-rules', [MasterManagementController::class, 'storeBonusRuleWeb'])->name('bonus-rules.store');

// Loss Rules
Route::get('/loss-rules', [MasterManagementController::class, 'indexLossRules'])->name('loss-rules.index');
Route::get('/loss-rules/create', [MasterManagementController::class, 'createLossRule'])->name('loss-rules.create');
Route::post('/loss-rules', [MasterManagementController::class, 'storeLossRuleWeb'])->name('loss-rules.store');


// FR2: Lots (Agreement Validation)
Route::get('/lots', [LotController::class, 'indexLots'])->name('lots.index');
Route::get('/lots/create', [LotController::class, 'createLot'])->name('lots.create');
Route::post('/lots', [LotController::class, 'storeWeb'])->name('lots.store');

// FR3 & FR4: FRN Processing (Arrival)
Route::get('/frns', [FrnController::class, 'indexFrns'])->name('frns.index');
Route::get('/frns/create', [FrnController::class, 'createFrn'])->name('frns.create');
Route::post('/frns', [FrnController::class, 'storeWeb'])->name('frns.store');

// FR5: Quality Validation Routes
Route::get('/quality-validation', [QualityValidationController::class, 'indexQuality'])->name('quality.index');
Route::get('/quality-validation/{lotNumber}/check', [QualityValidationController::class, 'checkQuality'])->name('quality.check');
Route::post('/quality-validation/{lotNumber}/submit', [QualityValidationController::class, 'submitQualityChecksWeb'])->name('quality.submit');

// Debit Note Approval
Route::post('/debit-note/{debitNote}/approve', [FrnController::class, 'approveDebitNote'])->name('debit-note.approve');

// Pricing Engine & Payment Processing
Route::post('/lots/{lot}/calculate-pricing', [PricingController::class, 'calculatePricing'])->name('lots.calculate-pricing');
Route::post('/lots/{lot}/approve-pricing', [PricingController::class, 'approvePricing'])->name('lots.approve-pricing');
Route::post('/lots/{lot}/process-payment', [PricingController::class, 'processPayment'])->name('lots.process-payment');
