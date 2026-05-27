<?php

declare(strict_types=1);

use App\Models\AiUsage;
use App\Models\Organization;
use App\Models\User;
use App\Services\ComplianceTimelineBuilder;
use Illuminate\Support\Carbon;

/**
 * Helper : usage RH "scoring CV" — déclenche R-H-02 (HAUT_RISQUE,
 * applicable à partir du 2 août 2026).
 */
function timelineUsageScoringCV(Organization $org): AiUsage
{
    $usage = AiUsage::factory()->for($org)->create([
        'type' => 'IA_SCORING',
        'domain' => 'RH',
        'name' => 'Scoring automatique des candidats',
    ]);

    foreach ([
        'dec' => 'FULL_AUTO',
        'pub' => 'EMPLOYES',
        'data_personal' => 'yes',
        'diff' => 'INTERNE',
        'human_oversight' => 'sometimes',
        'rh_usage' => 'SCORING_CANDIDATS',
    ] as $key => $value) {
        $usage->responses()->create(['variable_key' => $key, 'variable_value' => $value]);
    }

    return $usage->fresh('responses');
}

it('construit une timeline à 3 horizons (now, +1y, +2y)', function () {
    $user = User::factory()->create();
    $org = Organization::factory()->for($user)->create();
    timelineUsageScoringCV($org);

    $timeline = app(ComplianceTimelineBuilder::class)->build(
        $org->fresh('aiUsages.responses'),
        Carbon::parse('2026-05-26'),
    );

    expect($timeline)->toHaveCount(3);
    expect($timeline[0]['label'])->toBe('now');
    expect($timeline[1]['label'])->toBe('plus_1y');
    expect($timeline[2]['label'])->toBe('plus_2y');
});

it('détecte la bascule RISQUE_MINIMAL → HAUT_RISQUE au 2 août 2026', function () {
    $user = User::factory()->create();
    $org = Organization::factory()->for($user)->create();
    timelineUsageScoringCV($org);

    // Audit au 26 mai 2026 : R-H-02 (2026-08-02) pas encore applicable
    // → usage = RISQUE_MINIMAL (DEFAULT). À T+1y (2027-05-26), R-H-02 est
    // applicable → usage devient HAUT_RISQUE.
    $timeline = app(ComplianceTimelineBuilder::class)->build(
        $org->fresh('aiUsages.responses'),
        Carbon::parse('2026-05-26'),
    );

    expect($timeline[0]['counts']['RISQUE_MINIMAL'])->toBe(1);
    expect($timeline[0]['counts']['HAUT_RISQUE'])->toBe(0);
    expect($timeline[0]['transitions'])->toBe([]);

    expect($timeline[1]['counts']['HAUT_RISQUE'])->toBe(1);
    expect($timeline[1]['transitions'])->toHaveCount(1);
    expect($timeline[1]['transitions'][0]['from'])->toBe('RISQUE_MINIMAL');
    expect($timeline[1]['transitions'][0]['to'])->toBe('HAUT_RISQUE');
    expect($timeline[1]['transitions'][0]['regle_id'])->toBe('R-H-02');
});

it('ne signale aucune transition descendante (les règles ne se désappliquent pas)', function () {
    $user = User::factory()->create();
    $org = Organization::factory()->for($user)->create();
    timelineUsageScoringCV($org);

    // Toutes règles en vigueur depuis longtemps : pas de bascule.
    $timeline = app(ComplianceTimelineBuilder::class)->build(
        $org->fresh('aiUsages.responses'),
        Carbon::parse('2030-01-01'),
    );

    expect($timeline[0]['transitions'])->toBe([]);
    expect($timeline[1]['transitions'])->toBe([]);
    expect($timeline[2]['transitions'])->toBe([]);
});

it('initialise tous les compteurs de niveau à zéro même sans usage', function () {
    $user = User::factory()->create();
    $org = Organization::factory()->for($user)->create();

    $timeline = app(ComplianceTimelineBuilder::class)->build($org);

    expect($timeline[0]['counts'])->toBe([
        'INACCEPTABLE' => 0,
        'HAUT_RISQUE' => 0,
        'RISQUE_LIMITE' => 0,
        'RISQUE_MINIMAL' => 0,
        'NON_EVALUE' => 0,
    ]);
});
