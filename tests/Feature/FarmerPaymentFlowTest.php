<?php

use App\Models\Agreement;
use App\Models\Farmer;
use App\Models\Lot;
use App\Models\Organizer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('quality submission stores checks against agreement parameters', function () {
    $user = User::factory()->create();
    $organizer = Organizer::create(['name' => 'Organizer One']);
    $farmer = Farmer::create([
        'organizer_id' => $organizer->id,
        'name' => 'Farmer One',
        'phone_number' => '9999999999',
        'address' => 'Village',
    ]);
    $agreement = Agreement::create([
        'farmer_id' => $farmer->id,
        'start_date' => now()->subDay(),
        'end_date' => now()->addDay(),
        'rate' => 10,
        'bonus' => 0,
    ]);
    $parameter = $agreement->parameters()->create([
        'name' => 'moisture_content',
        'value' => 12,
    ]);
    $lot = Lot::create([
        'agreement_id' => $agreement->id,
        'lot_number' => 'LOT-QC-1',
        'quantity' => 100,
        'status' => 'accepted',
    ]);

    $this->actingAs($user)
        ->post(route('quality.submit', $lot->lot_number), [
            'final_quantity' => 98,
            'checks' => [
                ['parameter_id' => $parameter->id, 'observed_value' => 12],
            ],
        ])
        ->assertRedirect(route('quality.index'));

    $this->assertDatabaseHas('quality_checks', [
        'lot_number' => 'LOT-QC-1',
        'agreement_parameter_id' => $parameter->id,
        'status' => 'Accepted',
    ]);

    expect($lot->fresh()->qc_status)->toBe('Accepted');
});

test('blocked lots cannot be approved or paid through direct posts', function () {
    $user = User::factory()->create();
    $organizer = Organizer::create(['name' => 'Organizer Two']);
    $farmer = Farmer::create([
        'organizer_id' => $organizer->id,
        'name' => 'Farmer Two',
        'phone_number' => '8888888888',
        'address' => 'Village',
    ]);
    $agreement = Agreement::create([
        'farmer_id' => $farmer->id,
        'start_date' => now()->subDay(),
        'end_date' => now()->addDay(),
        'rate' => 10,
        'bonus' => 0,
    ]);
    $lot = Lot::create([
        'agreement_id' => $agreement->id,
        'lot_number' => 'LOT-BLOCKED-1',
        'quantity' => 100,
        'status' => 'accepted',
        'qc_status' => 'Rejected',
        'payment_blocked' => true,
        'net_payable' => 100,
    ]);

    $this->actingAs($user)
        ->post(route('lots.approve-pricing', $lot))
        ->assertSessionHas('error');

    expect($lot->fresh()->pricing_approved)->toBeFalse();

    $lot->update(['pricing_approved' => true]);

    $this->actingAs($user)
        ->post(route('lots.process-payment', $lot))
        ->assertSessionHas('error');

    expect($lot->fresh()->payment_status)->toBe('pending');
});
