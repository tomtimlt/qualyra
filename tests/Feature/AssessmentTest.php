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
        'bio_type' => 'IDENTIFICATION',
        'bio_realtime' => 'yes',
    ]);

    $result = app(AiActClassifier::class)->classify($usage);

    expect($result['niveau'])->toBe('INACCEPTABLE');
    expect($result['regle_id'])->toBe('R-I-05');
    expect($result['type_regle'])->toBe('TEXTE_EXPLICITE');
});

it('classifie HAUT_RISQUE un usage RH (R-H-02)', function () {
    // R-H-02 attend toutes les conditions de la matrice : DOM=RH, DEC≥AIDE_DEC,
    // PUB∋EMPLOYES, RH_USAGE dans la liste recrutement.
    $usage = usageWithAnswers('LLM_GEN', 'RH', [
        'finality' => 'Tri de CV',
        'data_personal' => 'yes',
        'dec' => 'AIDE_DEC',
        'pub' => 'EMPLOYES',
        'rh_usage' => 'TRI_CV',
    ]);

    $result = app(AiActClassifier::class)->classify($usage);

    expect($result['niveau'])->toBe('HAUT_RISQUE');
    expect($result['regle_id'])->toBe('R-H-02');
});

it('un usage interne sans condition matrice retombe sur RISQUE_MINIMAL', function () {
    // L'ancienne règle "haut_risque_decision_automatique_impactante" classait
    // ce cas en HAUT_RISQUE. La matrice v1.1 ne couvre pas ce cas — l'absence
    // de contrôle humain est une AGGRAVATION (non classificatoire) qui ne se
    // déclenche que si le niveau de base est déjà HAUT_RISQUE.
    $usage = usageWithAnswers('AUTRE', 'PROD_INT', [
        'impact_individual' => 'yes',
        'human_oversight' => 'never',
    ]);

    $result = app(AiActClassifier::class)->classify($usage);

    expect($result['niveau'])->toBe('RISQUE_MINIMAL');
    expect($result['regle_id'])->toBe('DEFAULT');
});

it('classifie RISQUE_LIMITE un chatbot interactif diffusé publiquement (R-L-01)', function () {
    $usage = usageWithAnswers('LLM_GEN', 'MARKETING', [
        'dec' => 'INFORMATIF',
        'pub' => 'CLIENTS',
        'diff' => 'PUBLIC',
        'interaction_directe' => 'OUI',
    ]);

    $result = app(AiActClassifier::class)->classify($usage);

    expect($result['niveau'])->toBe('RISQUE_LIMITE');
    expect($result['regle_id'])->toBe('R-L-01');
});

it('classifie RISQUE_LIMITE un deepfake diffusé (R-L-03)', function () {
    $usage = usageWithAnswers('IA_GEN', 'MARKETING', [
        'gen_contenu' => 'DEEPFAKE',
        'diff' => 'PUBLIC',
    ]);

    $result = app(AiActClassifier::class)->classify($usage);

    expect($result['niveau'])->toBe('RISQUE_LIMITE');
    expect($result['regle_id'])->toBe('R-L-03');
});

it('classifie RISQUE_MINIMAL en fallback (DEFAULT)', function () {
    $usage = usageWithAnswers('AUTRE', 'AUTRE', [
        'impact_individual' => 'no',
        'human_oversight' => 'always',
    ]);

    $result = app(AiActClassifier::class)->classify($usage);

    expect($result['niveau'])->toBe('RISQUE_MINIMAL');
    expect($result['regle_id'])->toBe('DEFAULT');
});

// ---------------------------------------------------------------------------
// Priorité : la règle la plus sévère gagne
// ---------------------------------------------------------------------------

it('donne priorité à INACCEPTABLE sur HAUT_RISQUE quand les deux matchent', function () {
    // Bio identification temps réel public ET DEC≥AIDE_DEC — R-I-05 (INACCEPTABLE)
    // ET R-H-01 (HAUT_RISQUE) sont tous deux candidats. R-I-05 doit gagner
    // car listé en premier dans la matrice (priorité par ordre).
    $usage = usageWithAnswers('IA_BIO', 'SANTE', [
        'bio_type' => 'IDENTIFICATION',
        'bio_realtime' => 'yes',
        'dec' => 'AIDE_DEC',
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
    // Identification temps réel public + DEC=AIDE_DEC → R-I-05 (INACCEPTABLE).
    // En retirant le flag temps réel, le cas redevient un système d'identif
    // biométrique avec aide à la décision → R-H-01 (HAUT_RISQUE).
    $usage = usageWithAnswers('IA_BIO', 'SECURITE', [
        'bio_type' => 'IDENTIFICATION',
        'bio_realtime' => 'yes',
        'dec' => 'AIDE_DEC',
    ]);

    $first = app(AiActClassifier::class)->persist($usage);
    expect($first->niveau)->toBe('INACCEPTABLE');

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
    foreach ([
        'bio_type' => 'IDENTIFICATION',
        'bio_realtime' => 'yes',
    ] as $key => $value) {
        $usage->responses()->create(['variable_key' => $key, 'variable_value' => $value]);
    }

    $this->actingAs($user)
        ->post("/usages/{$usage->id}/assessment")
        ->assertRedirect(route('usages.show', $usage));

    expect($usage->assessments()->first()->niveau)->toBe('INACCEPTABLE');
});
