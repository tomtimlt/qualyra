<?php

declare(strict_types=1);

use App\Models\AiUsage;
use App\Models\AiVendor;
use App\Models\Organization;
use App\Models\User;
use App\Services\AiActClassifier;
use App\Services\VendorComplianceEvaluator;

it('classe un vendor totalement conforme (UE) comme "complet"', function () {
    $vendor = AiVendor::factory()->create([
        'hors_ue' => false,
        'declaration_conformite_art47' => true,
        'dpa_art28_signe' => true,
        'cct_signees' => null,
    ]);

    $result = app(VendorComplianceEvaluator::class)->evaluate($vendor);

    expect($result['status'])->toBe('complet');
    expect($result['gaps'])->toBe([]);
});

it('classe un vendor totalement non conforme comme "manquant"', function () {
    $vendor = AiVendor::factory()->create([
        'hors_ue' => true,
        'declaration_conformite_art47' => false,
        'dpa_art28_signe' => false,
        'cct_signees' => false,
    ]);

    $result = app(VendorComplianceEvaluator::class)->evaluate($vendor);

    expect($result['status'])->toBe('manquant');
    expect($result['gaps'])->toHaveCount(3);
});

it('ne pénalise pas l\'absence de CCT pour un vendor UE', function () {
    $vendor = AiVendor::factory()->create([
        'hors_ue' => false,
        'declaration_conformite_art47' => true,
        'dpa_art28_signe' => true,
        'cct_signees' => null,
    ]);

    $result = app(VendorComplianceEvaluator::class)->evaluate($vendor);

    expect($result['cct_required'])->toBeFalse();
    expect($result['criteria']['cct'])->toBeTrue();
});

// ---------------------------------------------------------------------------
// Intégration avec AiActClassifier — règles R-VENDOR-*
// ---------------------------------------------------------------------------

function vendorUsage(string $type, string $domain, array $answers, ?AiVendor $vendor = null): AiUsage
{
    $user = User::factory()->create();
    $org = Organization::factory()->for($user)->create();
    $usage = AiUsage::factory()->for($org)->create([
        'type' => $type,
        'domain' => $domain,
    ]);

    if ($vendor !== null) {
        $usage->vendor()->associate($vendor)->save();
    }

    foreach ($answers as $k => $v) {
        $usage->responses()->create(['variable_key' => $k, 'variable_value' => $v]);
    }

    return $usage->fresh(['responses', 'vendor']);
}

it('déclenche R-VENDOR-TRANSFERT-RGPD quand vendor hors UE sans CCT', function () {
    $vendor = AiVendor::factory()->create([
        'hors_ue' => true,
        'cct_signees' => false,
        'declaration_conformite_art47' => true,
        'dpa_art28_signe' => true,
    ]);
    $usage = vendorUsage('LLM_GEN', 'PROD_INT', [
        'dec' => 'INFORMATIF',
        'pub' => 'AUCUN',
        'data_personal' => 'yes',
        'diff' => 'INTERNE',
        'human_oversight' => 'always',
    ], $vendor);

    $result = app(AiActClassifier::class)->classify($usage);
    $codes = collect($result['alertes'])->pluck('code')->all();

    expect($codes)->toContain('vendor_transfert_hors_ue_sans_cct');
});

it('déclenche R-VENDOR-ART47 uniquement si le niveau retenu est HAUT_RISQUE', function () {
    $vendor = AiVendor::factory()->create([
        'declaration_conformite_art47' => false,
    ]);

    // 1. Usage minimal : règle ne se déclenche pas (requires_niveau=HAUT_RISQUE)
    $usageMin = vendorUsage('LLM_GEN', 'PROD_INT', [
        'dec' => 'INFORMATIF',
        'pub' => 'AUCUN',
        'data_personal' => 'yes',
        'diff' => 'INTERNE',
        'human_oversight' => 'always',
    ], $vendor);
    $codesMin = collect(app(AiActClassifier::class)->classify($usageMin)['alertes'])->pluck('code')->all();
    expect($codesMin)->not->toContain('vendor_pas_de_declaration_art47');

    // 2. Usage haut risque (R-H-02) : la règle vendor s'ajoute.
    $usageHaut = vendorUsage('IA_SCORING', 'RH', [
        'dec' => 'FULL_AUTO',
        'pub' => 'EMPLOYES',
        'data_personal' => 'yes',
        'diff' => 'INTERNE',
        'human_oversight' => 'sometimes',
        'rh_usage' => 'SCORING_CANDIDATS',
    ], $vendor);
    $resultHaut = app(AiActClassifier::class)->classify($usageHaut);

    expect($resultHaut['niveau'])->toBe('HAUT_RISQUE');
    $codesHaut = collect($resultHaut['alertes'])->pluck('code')->all();
    expect($codesHaut)->toContain('vendor_pas_de_declaration_art47');
});

it('déclenche R-VENDOR-DPA-ART28 sur traitement données personnelles sans DPA', function () {
    $vendor = AiVendor::factory()->create([
        'dpa_art28_signe' => false,
    ]);
    $usage = vendorUsage('LLM_GEN', 'PROD_INT', [
        'dec' => 'INFORMATIF',
        'pub' => 'AUCUN',
        'data_personal' => 'yes',
        'diff' => 'INTERNE',
        'human_oversight' => 'always',
    ], $vendor);

    $codes = collect(app(AiActClassifier::class)->classify($usage)['alertes'])->pluck('code')->all();
    expect($codes)->toContain('vendor_pas_de_dpa_art28');
});

it('ne déclenche aucune règle vendor si l\'usage n\'a pas de vendor', function () {
    $usage = vendorUsage('LLM_GEN', 'PROD_INT', [
        'dec' => 'INFORMATIF',
        'pub' => 'AUCUN',
        'data_personal' => 'yes',
        'diff' => 'INTERNE',
        'human_oversight' => 'always',
    ], null);

    $codes = collect(app(AiActClassifier::class)->classify($usage)['alertes'])->pluck('code')->all();
    expect($codes)->not->toContain('vendor_transfert_hors_ue_sans_cct');
    expect($codes)->not->toContain('vendor_pas_de_dpa_art28');
    expect($codes)->not->toContain('vendor_saas_minimisation_donnees');
});
