<?php

declare(strict_types=1);

use App\Models\AiUsage;
use App\Models\Organization;
use App\Models\User;

/*
 * Suite end-to-end : reproduit les 5 cas de validation manuelle (étape B
 * du parcours utilisateur) au niveau HTTP, sans navigateur.
 *
 * Chaque test :
 *   1. Crée user + organization + AiUsage avec type/domain.
 *   2. POST /usages/{id}/questionnaire avec le payload du cas.
 *   3. POST /usages/{id}/assessment pour déclencher la classification.
 *   4. Lit l'Assessment persisté et vérifie niveau, règle, alertes.
 */

/**
 * @param  array<string, mixed>  $answers
 */
function e2eUsage(string $type, string $domain): array
{
    $user = User::factory()->create();
    $organization = Organization::factory()->for($user)->create();
    $aiUsage = AiUsage::factory()->for($organization)->create([
        'type' => $type,
        'domain' => $domain,
        'name' => "E2E {$type} {$domain}",
    ]);

    return [$user, $aiUsage];
}

// -----------------------------------------------------------------------------
// CAS 1 — IA biométrique reconnaissance émotions en RH → INACCEPTABLE (R-I-01)
// -----------------------------------------------------------------------------

it('CAS 1 — bio reco émotions en RH → INACCEPTABLE R-I-01', function () {
    [$user, $usage] = e2eUsage('IA_BIO', 'RH');

    $this->actingAs($user)->post("/usages/{$usage->id}/questionnaire", [
        'answers' => [
            'finality' => 'Test reconnaissance émotions',
            'dec' => 'AIDE_DEC',
            'pub' => ['EMPLOYES'],
            'data_personal' => 'yes',
            'data_sensitive' => 'yes',
            'diff' => 'INTERNE',
            'human_oversight' => 'sometimes',
            'impact_individual' => 'yes',
            'usage_prestations_essentielles' => 'NON',
            'bio_type' => 'RECOG_EMOTIONS',
            'bio_modality' => 'face',
            'bio_source_donnees' => 'FOURNIES_LICITES',
            'bio_attr_sensibles' => 'NON',
            'bio_realtime' => 'no',
            'bio_consent' => 'no',
            'rh_usage' => 'EVAL_PERFORMANCE',
        ],
    ])->assertRedirect(route('usages.show', $usage));

    $this->actingAs($user)->post("/usages/{$usage->id}/assessment")
        ->assertRedirect(route('usages.show', $usage));

    $assessment = $usage->assessments()->first();
    expect($assessment->niveau)->toBe('INACCEPTABLE');
    expect($assessment->regle_id)->toBe('R-I-01');
    expect($assessment->article)->toBe('Art. 5 §1 f)');
});

// -----------------------------------------------------------------------------
// CAS 2 — LLM en RH déclaré informatif → RISQUE_MINIMAL + flag ZG
// -----------------------------------------------------------------------------

it('CAS 2 — LLM RH informatif → RISQUE_MINIMAL + flag_zone_grise_rh_informatif', function () {
    [$user, $usage] = e2eUsage('LLM_GEN', 'RH');

    $this->actingAs($user)->post("/usages/{$usage->id}/questionnaire", [
        'answers' => [
            'finality' => 'ChatGPT résume des CV',
            'dec' => 'INFORMATIF',
            'pub' => ['EMPLOYES'],
            'data_personal' => 'yes',
            'data_sensitive' => 'no',
            'diff' => 'INTERNE',
            'human_oversight' => 'always',
            'impact_individual' => 'no',
            'usage_prestations_essentielles' => 'NON',
            'llm_provider' => 'openai',
            'llm_data_input' => 'CV PDF des candidats',
            'llm_output_published' => 'no',
            'interaction_directe' => 'NON',
            'rh_usage' => 'TRI_CV',
        ],
    ])->assertRedirect(route('usages.show', $usage));

    $this->actingAs($user)->post("/usages/{$usage->id}/assessment");

    $assessment = $usage->assessments()->first();
    expect($assessment->niveau)->toBe('RISQUE_MINIMAL');
    expect($assessment->regle_id)->toBe('DEFAULT');

    $codes = collect($assessment->alertes)->pluck('code')->all();
    expect($codes)->toContain('flag_zone_grise_rh_informatif');
});

// -----------------------------------------------------------------------------
// CAS 3 — Chatbot SAV public → RISQUE_LIMITE (R-L-01)
// -----------------------------------------------------------------------------

it('CAS 3 — Chatbot SAV public → RISQUE_LIMITE R-L-01', function () {
    [$user, $usage] = e2eUsage('LLM_GEN', 'MARKETING');

    $this->actingAs($user)->post("/usages/{$usage->id}/questionnaire", [
        'answers' => [
            'finality' => 'Chatbot SAV alimenté par GPT',
            'dec' => 'INFORMATIF',
            'pub' => ['CLIENTS'],
            'data_personal' => 'yes',
            'data_sensitive' => 'no',
            'diff' => 'PUBLIC',
            'human_oversight' => 'sometimes',
            'impact_individual' => 'no',
            'usage_prestations_essentielles' => 'NON',
            'llm_provider' => 'openai',
            'llm_data_input' => 'Questions clients',
            'llm_output_published' => 'no',
            'interaction_directe' => 'OUI',
            'techniques_subliminales' => 'NON',
            'persuasion_psychologique' => 'NON',
        ],
    ])->assertRedirect(route('usages.show', $usage));

    $this->actingAs($user)->post("/usages/{$usage->id}/assessment");

    $assessment = $usage->assessments()->first();
    expect($assessment->niveau)->toBe('RISQUE_LIMITE');
    expect($assessment->regle_id)->toBe('R-L-01');
    expect($assessment->article)->toBe('Art. 50 §1');
});

// -----------------------------------------------------------------------------
// CAS 4 — Copilot mails internes → RISQUE_MINIMAL (DEFAULT)
// -----------------------------------------------------------------------------

it('CAS 4 — Copilot mails internes → RISQUE_MINIMAL', function () {
    [$user, $usage] = e2eUsage('LLM_GEN', 'PROD_INT');

    $this->actingAs($user)->post("/usages/{$usage->id}/questionnaire", [
        'answers' => [
            'finality' => 'Copilot rédaction emails internes',
            'dec' => 'INFORMATIF',
            'pub' => ['AUCUN'],
            'data_personal' => 'yes',
            'data_sensitive' => 'no',
            'diff' => 'INTERNE',
            'human_oversight' => 'always',
            'impact_individual' => 'no',
            'usage_prestations_essentielles' => 'NON',
            'llm_provider' => 'openai',
            'llm_data_input' => 'Drafts d\'emails',
            'llm_output_published' => 'no',
            'interaction_directe' => 'NON',
        ],
    ])->assertRedirect(route('usages.show', $usage));

    $this->actingAs($user)->post("/usages/{$usage->id}/assessment");

    $assessment = $usage->assessments()->first();
    expect($assessment->niveau)->toBe('RISQUE_MINIMAL');
    expect($assessment->regle_id)->toBe('DEFAULT');
});

// -----------------------------------------------------------------------------
// CAS 5 — Multi-checkbox PUB : persistence CSV + pré-cochage au re-rendu
// -----------------------------------------------------------------------------

it('CAS 5 — PUB multi-valeur persisté en CSV et pré-coché au re-rendu', function () {
    [$user, $usage] = e2eUsage('LLM_GEN', 'PROD_INT');

    // Soumission avec deux valeurs PUB
    $this->actingAs($user)->post("/usages/{$usage->id}/questionnaire", [
        'answers' => [
            'finality' => 'Test multi-PUB',
            'dec' => 'INFORMATIF',
            'pub' => ['EMPLOYES', 'CLIENTS'],
            'data_personal' => 'yes',
            'data_sensitive' => 'no',
            'diff' => 'INTERNE',
            'human_oversight' => 'always',
            'impact_individual' => 'no',
            'usage_prestations_essentielles' => 'NON',
            'llm_provider' => 'openai',
            'llm_data_input' => 'Test',
            'llm_output_published' => 'no',
            'interaction_directe' => 'NON',
        ],
    ])->assertRedirect(route('usages.show', $usage));

    // Vérification BDD : une seule réponse pour la clé pub, en CSV.
    $stored = $usage->responses()->where('variable_key', 'pub')->first();
    expect($stored->variable_value)->toBe('EMPLOYES,CLIENTS');
    expect($usage->responses()->where('variable_key', 'pub')->count())->toBe(1);

    // Re-rendu du formulaire : les deux checkboxes EMPLOYES et CLIENTS doivent
    // être pré-cochées, AUCUN/GRAND_PUBLIC/VULNERABLES doivent être décochées.
    $response = $this->actingAs($user)->get("/usages/{$usage->id}/questionnaire");
    $response->assertOk();

    $html = $response->getContent();
    // Inputs checkbox attendus avec attribut checked sur les valeurs sélectionnées.
    expect($html)->toContain('value="EMPLOYES"');
    expect($html)->toContain('value="CLIENTS"');

    // Construction d'un regex qui valide qu'EMPLOYES et CLIENTS sont cochés
    // (l'attribut checked est rendu par la directive Blade @checked).
    expect($html)->toMatch('/value="EMPLOYES"[^>]*checked/');
    expect($html)->toMatch('/value="CLIENTS"[^>]*checked/');

    // Les autres options ne doivent PAS être pré-cochées.
    expect($html)->not->toMatch('/value="AUCUN"[^>]*checked/');
    expect($html)->not->toMatch('/value="GRAND_PUBLIC"[^>]*checked/');
    expect($html)->not->toMatch('/value="VULNERABLES"[^>]*checked/');
});
