<?php

declare(strict_types=1);

use App\Models\AiUsage;
use App\Models\Organization;
use App\Models\User;
use App\Services\AiActClassifier;

/**
 * Helper : crée un AiUsage avec ses réponses préchargées.
 *
 * @param  array<string, string>  $answers
 */
function usageWithAnswers(string $type, string $domain, array $answers): AiUsage
{
    $user = User::factory()->create();
    $organization = Organization::factory()->for($user)->create();
    $aiUsage = AiUsage::factory()->for($organization)->create([
        'type' => $type,
        'domain' => $domain,
    ]);

    foreach ($answers as $key => $value) {
        $aiUsage->responses()->create([
            'variable_key' => $key,
            'variable_value' => $value,
        ]);
    }

    return $aiUsage->fresh('responses');
}

// ---------------------------------------------------------------------------
// Classification : 4 niveaux AI Act
// ---------------------------------------------------------------------------

it('classifie INACCEPTABLE une IA biométrique temps réel public', function () {
    $usage = usageWithAnswers('IA_BIO', 'SECURITE', [
        'bio_realtime' => 'yes',
    ]);

    $result = app(AiActClassifier::class)->classify($usage);

    expect($result['niveau'])->toBe('INACCEPTABLE');
    expect($result['regle_id'])->toBe('art5_bio_realtime_public');
    expect($result['type_regle'])->toBe('TEXTE_EXPLICITE');
});

it('classifie HAUT_RISQUE un usage RH (Annexe III)', function () {
    $usage = usageWithAnswers('LLM_GEN', 'RH', [
        'finality' => 'Tri de CV',
        'data_personal' => 'yes',
    ]);

    $result = app(AiActClassifier::class)->classify($usage);

    expect($result['niveau'])->toBe('HAUT_RISQUE');
    expect($result['regle_id'])->toBe('annexe3_recrutement');
});

it('classifie HAUT_RISQUE par interprétation : décision auto + impact + pas de supervision', function () {
    $usage = usageWithAnswers('AUTRE', 'PROD_INT', [
        'impact_individual' => 'yes',
        'human_oversight' => 'never',
    ]);

    $result = app(AiActClassifier::class)->classify($usage);

    expect($result['niveau'])->toBe('HAUT_RISQUE');
    expect($result['type_regle'])->toBe('INTERPRETATION');
});

it('classifie RISQUE_LIMITE un LLM générique sans flag haut risque', function () {
    $usage = usageWithAnswers('LLM_GEN', 'PROD_INT', [
        'data_personal' => 'no',
        'impact_individual' => 'no',
    ]);

    $result = app(AiActClassifier::class)->classify($usage);

    expect($result['niveau'])->toBe('RISQUE_LIMITE');
    expect($result['regle_id'])->toBe('art50_chatbot_llm');
});

it('classifie RISQUE_LIMITE deepfake spécifiquement', function () {
    $usage = usageWithAnswers('IA_GEN', 'MARKETING', [
        'gen_deepfake_risk' => 'yes',
    ]);

    $result = app(AiActClassifier::class)->classify($usage);

    expect($result['niveau'])->toBe('RISQUE_LIMITE');
    expect($result['regle_id'])->toBe('art50_deepfake');
});

it('classifie RISQUE_MINIMAL en fallback (type AUTRE, sans condition matche)', function () {
    $usage = usageWithAnswers('AUTRE', 'AUTRE', [
        'impact_individual' => 'no',
        'human_oversight' => 'always',
    ]);

    $result = app(AiActClassifier::class)->classify($usage);

    expect($result['niveau'])->toBe('RISQUE_MINIMAL');
    expect($result['regle_id'])->toBe('fallback_risque_minimal');
});

// ---------------------------------------------------------------------------
// Priorité : la règle la plus sévère gagne
// ---------------------------------------------------------------------------

it('donne priorité à INACCEPTABLE sur HAUT_RISQUE quand les deux matchent', function () {
    // Bio temps réel public ET secteur SANTE — les deux matchent (INACCEPTABLE + HAUT_RISQUE)
    // mais l'INACCEPTABLE doit gagner car listé en premier dans la matrice.
    $usage = usageWithAnswers('IA_BIO', 'SANTE', [
        'bio_realtime' => 'yes',
    ]);

    $result = app(AiActClassifier::class)->classify($usage);

    expect($result['niveau'])->toBe('INACCEPTABLE');
});

// ---------------------------------------------------------------------------
// Alertes complémentaires
// ---------------------------------------------------------------------------

it('remonte une alerte RGPD Article 9 sur données sensibles', function () {
    $usage = usageWithAnswers('LLM_GEN', 'SANTE', [
        'data_sensitive' => 'yes',
    ]);

    $alertes = app(AiActClassifier::class)->classify($usage)['alertes'];

    expect($alertes)->toHaveCount(1);
    expect($alertes[0]['code'])->toBe('rgpd_art9');
});

it('remonte une alerte RGPD Article 22 sur décision auto + données personnelles', function () {
    $usage = usageWithAnswers('LLM_GEN', 'PROD_INT', [
        'data_personal' => 'yes',
        'human_oversight' => 'never',
    ]);

    $alertes = app(AiActClassifier::class)->classify($usage)['alertes'];

    expect(collect($alertes)->pluck('code'))->toContain('rgpd_art22');
});

// ---------------------------------------------------------------------------
// Persistance
// ---------------------------------------------------------------------------

it('persiste un Assessment et remplace l\'ancien à chaque calcul', function () {
    $usage = usageWithAnswers('IA_BIO', 'SECURITE', [
        'bio_realtime' => 'yes',
    ]);

    $first = app(AiActClassifier::class)->persist($usage);
    expect($first->niveau)->toBe('INACCEPTABLE');

    // Modification : on retire le flag temps réel → reclassification HAUT_RISQUE
    $usage->responses()->where('variable_key', 'bio_realtime')->update(['variable_value' => 'no']);
    $second = app(AiActClassifier::class)->persist($usage->fresh('responses'));

    expect($second->niveau)->toBe('HAUT_RISQUE');
    expect($usage->assessments()->count())->toBe(1);
});

// ---------------------------------------------------------------------------
// Endpoint POST /usages/{aiUsage}/assessment
// ---------------------------------------------------------------------------

it('refuse l\'évaluation aux invités', function () {
    $aiUsage = AiUsage::factory()->create();

    $this->post("/usages/{$aiUsage->id}/assessment")->assertRedirect('/login');
});

it('renvoie 403 sur un usage d\'une autre organisation', function () {
    $user = User::factory()->create();
    Organization::factory()->for($user)->create();
    $autreUsage = AiUsage::factory()->create();

    $this->actingAs($user)
        ->post("/usages/{$autreUsage->id}/assessment")
        ->assertForbidden();
});

it('redirige vers le questionnaire si pas de réponses', function () {
    $user = User::factory()->create();
    $organization = Organization::factory()->for($user)->create();
    $usage = AiUsage::factory()->for($organization)->create();

    $this->actingAs($user)
        ->post("/usages/{$usage->id}/assessment")
        ->assertRedirect(route('usages.questionnaire.show', $usage));

    expect($usage->assessments()->count())->toBe(0);
});

it('crée et persiste l\'évaluation puis redirige vers la fiche usage', function () {
    $user = User::factory()->create();
    $organization = Organization::factory()->for($user)->create();
    $usage = AiUsage::factory()->for($organization)->create([
        'type' => 'IA_BIO',
        'domain' => 'SECURITE',
    ]);
    $usage->responses()->create([
        'variable_key' => 'bio_realtime',
        'variable_value' => 'yes',
    ]);

    $this->actingAs($user)
        ->post("/usages/{$usage->id}/assessment")
        ->assertRedirect(route('usages.show', $usage));

    expect($usage->assessments()->first()->niveau)->toBe('INACCEPTABLE');
});
