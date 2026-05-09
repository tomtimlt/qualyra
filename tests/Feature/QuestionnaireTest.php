<?php

declare(strict_types=1);

use App\Models\AiUsage;
use App\Models\Organization;
use App\Models\User;

function userWithUsage(string $type = 'LLM_GEN'): array
{
    $user = User::factory()->create();
    $organization = Organization::factory()->for($user)->create();
    $aiUsage = AiUsage::factory()->for($organization)->create(['type' => $type]);

    return [$user, $aiUsage];
}

// ---------------------------------------------------------------------------
// Accès anonyme
// ---------------------------------------------------------------------------

it('refuse l\'accès au questionnaire aux invités', function () {
    $aiUsage = AiUsage::factory()->create();

    $this->get("/usages/{$aiUsage->id}/questionnaire")->assertRedirect('/login');
    $this->post("/usages/{$aiUsage->id}/questionnaire", [])->assertRedirect('/login');
});

// ---------------------------------------------------------------------------
// Affichage du formulaire — questions dynamiques par type
// ---------------------------------------------------------------------------

it('affiche les questions communes + spécifiques au type LLM_GEN', function () {
    [$user, $aiUsage] = userWithUsage('LLM_GEN');

    $response = $this->actingAs($user)->get("/usages/{$aiUsage->id}/questionnaire");

    $response->assertOk();
    // Question commune
    $response->assertSee('Finalité principale', false);
    // Question spécifique LLM
    $response->assertSee('Fournisseur du LLM utilisé', false);
    // Question NON spécifique LLM (présente sur d'autres types)
    $response->assertDontSee('Modalité biométrique utilisée', false);
});

it('affiche les questions spécifiques à IA_BIO et pas celles des autres types', function () {
    [$user, $aiUsage] = userWithUsage('IA_BIO');

    $response = $this->actingAs($user)->get("/usages/{$aiUsage->id}/questionnaire");

    $response->assertOk();
    $response->assertSee('Modalité biométrique utilisée', false);
    $response->assertDontSee('Fournisseur du LLM utilisé', false);
});

// ---------------------------------------------------------------------------
// Sauvegarde et upsert
// ---------------------------------------------------------------------------

it('persiste les réponses du questionnaire', function () {
    [$user, $aiUsage] = userWithUsage('LLM_GEN');

    $payload = [
        'answers' => [
            'finality' => 'Tri automatique des CV',
            'data_personal' => 'yes',
            'data_sensitive' => 'no',
            'human_oversight' => 'always',
            'impact_individual' => 'yes',
            'llm_provider' => 'anthropic',
            'llm_data_input' => 'CV au format PDF',
            'llm_output_published' => 'no',
        ],
    ];

    $this->actingAs($user)
        ->post("/usages/{$aiUsage->id}/questionnaire", $payload)
        ->assertRedirect(route('usages.show', $aiUsage));

    $this->assertDatabaseHas('responses', [
        'ai_usage_id' => $aiUsage->id,
        'variable_key' => 'llm_provider',
        'variable_value' => 'anthropic',
    ]);
    $this->assertDatabaseHas('responses', [
        'ai_usage_id' => $aiUsage->id,
        'variable_key' => 'finality',
        'variable_value' => 'Tri automatique des CV',
    ]);
});

it('met à jour une réponse existante (upsert) sans dupliquer en BDD', function () {
    [$user, $aiUsage] = userWithUsage('LLM_GEN');

    $base = [
        'finality' => 'V1',
        'data_personal' => 'yes',
        'data_sensitive' => 'no',
        'human_oversight' => 'always',
        'impact_individual' => 'yes',
        'llm_provider' => 'openai',
        'llm_data_input' => 'Données A',
        'llm_output_published' => 'no',
    ];

    $this->actingAs($user)->post("/usages/{$aiUsage->id}/questionnaire", ['answers' => $base]);

    $base['llm_provider'] = 'anthropic';
    $base['finality'] = 'V2';
    $this->actingAs($user)->post("/usages/{$aiUsage->id}/questionnaire", ['answers' => $base]);

    expect($aiUsage->responses()->where('variable_key', 'llm_provider')->count())->toBe(1);
    expect($aiUsage->responses()->where('variable_key', 'llm_provider')->first()->variable_value)
        ->toBe('anthropic');
    expect($aiUsage->responses()->where('variable_key', 'finality')->first()->variable_value)
        ->toBe('V2');
});

it('rejette une réponse hors de la liste d\'options', function () {
    [$user, $aiUsage] = userWithUsage('LLM_GEN');

    $this->actingAs($user)->post("/usages/{$aiUsage->id}/questionnaire", [
        'answers' => [
            'finality' => 'OK',
            'data_personal' => 'maybe', // valeur non listée
            'data_sensitive' => 'no',
            'human_oversight' => 'always',
            'impact_individual' => 'yes',
            'llm_provider' => 'openai',
            'llm_data_input' => 'X',
            'llm_output_published' => 'no',
        ],
    ])->assertSessionHasErrors('answers.data_personal');
});

it('exige les champs obligatoires', function () {
    [$user, $aiUsage] = userWithUsage('LLM_GEN');

    $this->actingAs($user)->post("/usages/{$aiUsage->id}/questionnaire", [
        'answers' => [],
    ])->assertSessionHasErrors([
        'answers.finality',
        'answers.data_personal',
        'answers.llm_provider',
    ]);
});

it('ignore les clés inconnues même si la validation laisse passer', function () {
    [$user, $aiUsage] = userWithUsage('LLM_GEN');

    $this->actingAs($user)->post("/usages/{$aiUsage->id}/questionnaire", [
        'answers' => [
            'finality' => 'OK',
            'data_personal' => 'yes',
            'data_sensitive' => 'no',
            'human_oversight' => 'always',
            'impact_individual' => 'yes',
            'llm_provider' => 'openai',
            'llm_data_input' => 'X',
            'llm_output_published' => 'no',
            'evil_injected_key' => 'boom',
        ],
    ]);

    $this->assertDatabaseMissing('responses', [
        'ai_usage_id' => $aiUsage->id,
        'variable_key' => 'evil_injected_key',
    ]);
});

// ---------------------------------------------------------------------------
// SÉCURITÉ : isolation tenant sur le questionnaire
// ---------------------------------------------------------------------------

it('renvoie 403 si un user tente d\'accéder au questionnaire d\'une autre organisation', function () {
    [$user] = userWithUsage('LLM_GEN');
    $autreUsage = AiUsage::factory()->create(['type' => 'LLM_GEN']);

    $this->actingAs($user)
        ->get("/usages/{$autreUsage->id}/questionnaire")
        ->assertForbidden();
});

it('renvoie 403 si un user tente de répondre au questionnaire d\'une autre organisation', function () {
    [$user] = userWithUsage('LLM_GEN');
    $autreUsage = AiUsage::factory()->create(['type' => 'LLM_GEN']);

    $this->actingAs($user)->post("/usages/{$autreUsage->id}/questionnaire", [
        'answers' => ['finality' => 'hijack'],
    ])->assertForbidden();

    $this->assertDatabaseMissing('responses', [
        'ai_usage_id' => $autreUsage->id,
        'variable_value' => 'hijack',
    ]);
});
