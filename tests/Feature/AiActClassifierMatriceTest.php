<?php

declare(strict_types=1);

use App\Models\AiUsage;
use App\Models\Organization;
use App\Models\User;
use App\Services\AiActClassifier;

/*
 * Suite de réception — 5 scénarios de l'Annexe 3 de la matrice v1.1.
 *
 * Cette suite est le contrat de cohérence du moteur : si un de ces tests
 * échoue, le moteur n'est pas livrable. Voir docs/Matrice de Décision AI Act.md
 * (section "ANNEXE 3 — Tests de cohérence").
 */

/**
 * Helper : crée un AiUsage avec ses réponses préchargées.
 *
 * @param  array<string, string>  $answers
 */
function matriceUsage(string $type, string $domain, array $answers): AiUsage
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

// -----------------------------------------------------------------------------
// Scénario 1 — ChatGPT pour résumer des CV (DEC=INFORMATIF)
//
// Variables : TYPE=LLM_GEN, DOM=RH, DEC=INFORMATIF, PUB={EMPLOYES},
//             DATA=PERSO_STD, DIFF=INTERNE, CTRL=SYSTEMATIQUE, RH_USAGE=TRI_CV
//
// Résultat retenu : RISQUE_MINIMAL + alerte FLAG_ZONE_GRISE (R-H-BORDERLINE).
// L'Annexe 3 énonce "HAUT_RISQUE (avec ZG-01 à signaler)" mais le pseudo-code
// patché v1.1 stipule explicitement que R-H-BORDERLINE est non classificatoire
// et continue vers le défaut. On suit le pseudo-code, conformément à la
// décision retenue lors de l'implémentation. Le niveau de base est donc
// RISQUE_MINIMAL ; l'alerte forte ZG-01 doit alerter le déployeur.
// -----------------------------------------------------------------------------

it('scénario 1 — LLM résume CV (RH informatif) → RISQUE_MINIMAL + alerte FLAG_ZONE_GRISE', function () {
    $usage = matriceUsage('LLM_GEN', 'RH', [
        'dec' => 'INFORMATIF',
        'pub' => 'EMPLOYES',
        'data_personal' => 'yes',
        'diff' => 'INTERNE',
        'human_oversight' => 'always',
        'rh_usage' => 'TRI_CV',
    ]);

    $result = app(AiActClassifier::class)->classify($usage);

    expect($result['niveau'])->toBe('RISQUE_MINIMAL');
    expect($result['regle_id'])->toBe('DEFAULT');

    $codes = collect($result['alertes'])->pluck('code')->all();
    expect($codes)->toContain('flag_zone_grise_rh_informatif');
});

// -----------------------------------------------------------------------------
// Scénario 2 — IA score automatique des CV (CTRL=AUCUN)
//
// Variables : TYPE=IA_SCORING, DOM=RH, DEC=FULL_AUTO, PUB={EMPLOYES},
//             DATA=PERSO_STD, DIFF=INTERNE, CTRL=AUCUN, RH_USAGE=SCORING_CANDIDATS
// Attendu : HAUT_RISQUE (R-H-02) + alerte AGGRAVATION
// -----------------------------------------------------------------------------

it('scénario 2 — IA score CV automatique sans contrôle → HAUT_RISQUE + AGGRAVATION', function () {
    $usage = matriceUsage('IA_SCORING', 'RH', [
        'dec' => 'FULL_AUTO',
        'pub' => 'EMPLOYES',
        'data_personal' => 'yes',
        'diff' => 'INTERNE',
        'human_oversight' => 'never',
        'rh_usage' => 'SCORING_CANDIDATS',
    ]);

    $result = app(AiActClassifier::class)->classify($usage);

    expect($result['niveau'])->toBe('HAUT_RISQUE');
    expect($result['regle_id'])->toBe('R-H-02');
    expect($result['article'])->toContain('Annexe III §4 a)');

    $codes = collect($result['alertes'])->pluck('code')->all();
    expect($codes)->toContain('aggravation_pas_de_controle_humain');
});

// -----------------------------------------------------------------------------
// Scénario 3 — Chatbot SAV
//
// Variables : TYPE=LLM_GEN, DOM=MARKETING, DEC=INFORMATIF, PUB={CLIENTS},
//             DATA=PERSO_STD, DIFF=PUBLIC, CTRL=ECHANTILLON, INTERACTION_DIRECTE=OUI
// Attendu : RISQUE_LIMITE (R-L-01)
// -----------------------------------------------------------------------------

it('scénario 3 — Chatbot SAV public → RISQUE_LIMITE (R-L-01)', function () {
    $usage = matriceUsage('LLM_GEN', 'MARKETING', [
        'dec' => 'INFORMATIF',
        'pub' => 'CLIENTS',
        'data_personal' => 'yes',
        'diff' => 'PUBLIC',
        'human_oversight' => 'sometimes',
        'interaction_directe' => 'OUI',
    ]);

    $result = app(AiActClassifier::class)->classify($usage);

    expect($result['niveau'])->toBe('RISQUE_LIMITE');
    expect($result['regle_id'])->toBe('R-L-01');
    expect($result['article'])->toBe('Art. 50 §1');
});

// -----------------------------------------------------------------------------
// Scénario 4 — Reconnaissance d'émotions des employés en visio
//
// Variables : TYPE=IA_BIO, DOM=RH, DEC=AIDE_DEC, PUB={EMPLOYES}, DATA=SENSIBLE,
//             DIFF=INTERNE, CTRL=ECHANTILLON, BIO_TYPE=RECOG_EMOTIONS
// Attendu : INACCEPTABLE (R-I-01)
// -----------------------------------------------------------------------------

it('scénario 4 — Reconnaissance émotions employés → INACCEPTABLE (R-I-01)', function () {
    $usage = matriceUsage('IA_BIO', 'RH', [
        'dec' => 'AIDE_DEC',
        'pub' => 'EMPLOYES',
        'data_sensitive' => 'yes',
        'diff' => 'INTERNE',
        'human_oversight' => 'sometimes',
        'bio_type' => 'RECOG_EMOTIONS',
    ]);

    $result = app(AiActClassifier::class)->classify($usage);

    expect($result['niveau'])->toBe('INACCEPTABLE');
    expect($result['regle_id'])->toBe('R-I-01');
    expect($result['article'])->toBe('Art. 5 §1 f)');
    expect($result['type_regle'])->toBe('TEXTE_EXPLICITE');
});

// -----------------------------------------------------------------------------
// Scénario 5 — Copilot pour mails internes
//
// Variables : TYPE=LLM_GEN, DOM=PROD_INT, DEC=INFORMATIF, PUB=AUCUN,
//             DATA=PERSO_STD, DIFF=INTERNE, CTRL=SYSTEMATIQUE
// Attendu : RISQUE_MINIMAL (DEFAULT)
// -----------------------------------------------------------------------------

it('scénario 5 — Copilot mails internes → RISQUE_MINIMAL', function () {
    $usage = matriceUsage('LLM_GEN', 'PROD_INT', [
        'dec' => 'INFORMATIF',
        'pub' => 'AUCUN',
        'data_personal' => 'yes',
        'diff' => 'INTERNE',
        'human_oversight' => 'always',
    ]);

    $result = app(AiActClassifier::class)->classify($usage);

    expect($result['niveau'])->toBe('RISQUE_MINIMAL');
    expect($result['regle_id'])->toBe('DEFAULT');
});

// -----------------------------------------------------------------------------
// Garde NON_EVALUE — un usage sans réponses ne doit JAMAIS retomber
// silencieusement sur RISQUE_MINIMAL.
// -----------------------------------------------------------------------------

it('usage sans aucune réponse → NON_EVALUE (pas de fallback silencieux)', function () {
    $user = User::factory()->create();
    $organization = Organization::factory()->for($user)->create();
    $usage = AiUsage::factory()->for($organization)->create();

    $result = app(AiActClassifier::class)->classify($usage->fresh('responses'));

    expect($result['niveau'])->toBe('NON_EVALUE');
    expect($result['regle_id'])->toBe('NON_EVALUE');
    expect($result['alertes'])->toBe([]);
});

it('persist() lève une exception si on tente de persister un NON_EVALUE', function () {
    $user = User::factory()->create();
    $organization = Organization::factory()->for($user)->create();
    $usage = AiUsage::factory()->for($organization)->create();

    expect(fn () => app(AiActClassifier::class)->persist($usage->fresh('responses')))
        ->toThrow(RuntimeException::class);
});
