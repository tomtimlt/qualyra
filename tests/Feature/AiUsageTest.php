<?php

declare(strict_types=1);

use App\Models\AiUsage;
use App\Models\Organization;
use App\Models\User;

/**
 * Helper : crée un user avec son organisation rattachée et le retourne.
 * Centralise la création du contexte d'authentification pour tous les
 * tests CRUD ci-dessous.
 */
function userWithOrganization(): array
{
    $user = User::factory()->create();
    $organization = Organization::factory()->for($user)->create();

    return [$user, $organization];
}

// ---------------------------------------------------------------------------
// Accès anonyme (guest) — toutes les routes doivent rediriger vers le login
// ---------------------------------------------------------------------------

it('refuse toutes les routes usages aux invités', function () {
    $aiUsage = AiUsage::factory()->create();

    $this->get('/usages')->assertRedirect('/login');
    $this->get('/usages/create')->assertRedirect('/login');
    $this->get("/usages/{$aiUsage->id}")->assertRedirect('/login');
    $this->get("/usages/{$aiUsage->id}/edit")->assertRedirect('/login');
    $this->post('/usages', [])->assertRedirect('/login');
    $this->patch("/usages/{$aiUsage->id}", [])->assertRedirect('/login');
    $this->delete("/usages/{$aiUsage->id}")->assertRedirect('/login');
});

// ---------------------------------------------------------------------------
// Pré-requis : avoir une organisation pour utiliser le module
// ---------------------------------------------------------------------------

it('redirige vers la création d\'organisation si le user n\'en a pas (index)', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->get('/usages')->assertRedirect(route('organization.create'));
});

it('refuse la création d\'usage si le user n\'a pas encore d\'organisation', function () {
    $user = User::factory()->create();

    // GET /usages/create redirige (le contrôleur intercepte avant la policy)
    $this->actingAs($user)->get('/usages/create')->assertRedirect(route('organization.create'));

    // POST /usages est bloqué par la policy (403)
    $this->actingAs($user)->post('/usages', [
        'name' => 'Test',
        'type' => 'LLM_GEN',
        'domain' => 'RH',
    ])->assertForbidden();
});

// ---------------------------------------------------------------------------
// CRUD nominal pour un user avec organisation
// ---------------------------------------------------------------------------

it('liste uniquement les usages de l\'organisation du user', function () {
    [$user, $organization] = userWithOrganization();
    $monUsage = AiUsage::factory()->for($organization)->create(['name' => 'Mon usage']);

    $autreUser = User::factory()->create();
    $autreOrg = Organization::factory()->for($autreUser)->create();
    AiUsage::factory()->for($autreOrg)->create(['name' => 'Usage de l\'autre organisation']);

    $response = $this->actingAs($user)->get('/usages');

    $response->assertOk();
    $response->assertSee('Mon usage');
    $response->assertDontSee('Usage de l\'autre organisation');
});

it('crée un usage rattaché à l\'organisation du user (jamais à une autre)', function () {
    [$user, $organization] = userWithOrganization();

    // Tentative de forcer organization_id via mass assignment d'une autre org
    $autreOrg = Organization::factory()->create();

    $response = $this->actingAs($user)->post('/usages', [
        'name' => 'Mon nouvel usage',
        'description' => 'Description test',
        'type' => 'LLM_GEN',
        'domain' => 'RH',
        'organization_id' => $autreOrg->id, // doit être ignoré
    ]);

    $response->assertRedirect(route('usages.index'));

    $this->assertDatabaseHas('ai_usages', [
        'organization_id' => $organization->id,
        'name' => 'Mon nouvel usage',
    ]);
    $this->assertDatabaseMissing('ai_usages', [
        'organization_id' => $autreOrg->id,
        'name' => 'Mon nouvel usage',
    ]);
});

it('valide les champs obligatoires à la création', function () {
    [$user] = userWithOrganization();

    $this->actingAs($user)->post('/usages', [
        'name' => '',
        'type' => 'TYPE_INVALIDE',
        'domain' => 'DOMAINE_INVALIDE',
    ])->assertSessionHasErrors(['name', 'type', 'domain']);
});

it('met à jour un usage existant de l\'organisation du user', function () {
    [$user, $organization] = userWithOrganization();
    $usage = AiUsage::factory()->for($organization)->create(['name' => 'Avant']);

    $response = $this->actingAs($user)->patch("/usages/{$usage->id}", [
        'name' => 'Après',
        'type' => 'IA_GEN',
        'domain' => 'MARKETING',
    ]);

    $response->assertRedirect(route('usages.index'));
    expect($usage->fresh()->name)->toBe('Après');
    expect($usage->fresh()->type)->toBe('IA_GEN');
});

it('affiche le détail d\'un usage de l\'organisation du user', function () {
    [$user, $organization] = userWithOrganization();
    $usage = AiUsage::factory()->for($organization)->create([
        'name' => 'ChatGPT pour les CV',
        'description' => 'Aide au tri des candidatures.',
    ]);

    $response = $this->actingAs($user)->get("/usages/{$usage->id}");

    $response->assertOk();
    $response->assertSee('ChatGPT pour les CV');
    $response->assertSee('Aide au tri des candidatures.');
});

it('supprime un usage de l\'organisation du user', function () {
    [$user, $organization] = userWithOrganization();
    $usage = AiUsage::factory()->for($organization)->create();

    $response = $this->actingAs($user)->delete("/usages/{$usage->id}");

    $response->assertRedirect(route('usages.index'));
    $this->assertDatabaseMissing('ai_usages', ['id' => $usage->id]);
});

// ---------------------------------------------------------------------------
// SÉCURITÉ CRITIQUE : isolation entre organisations
// Un user ne doit JAMAIS pouvoir voir/modifier/supprimer un usage
// d'une autre organisation. Test de non-régression RGPD/AI Act.
// ---------------------------------------------------------------------------

it('renvoie 403 quand un user tente de voir un usage d\'une autre organisation', function () {
    [$user] = userWithOrganization();
    $autreUsage = AiUsage::factory()->create(); // factory crée une autre org

    $this->actingAs($user)
        ->get("/usages/{$autreUsage->id}")
        ->assertForbidden();
});

it('renvoie 403 quand un user tente d\'éditer un usage d\'une autre organisation', function () {
    [$user] = userWithOrganization();
    $autreUsage = AiUsage::factory()->create();

    $this->actingAs($user)
        ->get("/usages/{$autreUsage->id}/edit")
        ->assertForbidden();
});

it('renvoie 403 quand un user tente de mettre à jour un usage d\'une autre organisation', function () {
    [$user] = userWithOrganization();
    $autreUsage = AiUsage::factory()->create(['name' => 'Original']);

    $this->actingAs($user)->patch("/usages/{$autreUsage->id}", [
        'name' => 'Tentative de hijack',
        'type' => 'LLM_GEN',
        'domain' => 'RH',
    ])->assertForbidden();

    expect($autreUsage->fresh()->name)->toBe('Original');
});

it('renvoie 403 quand un user tente de supprimer un usage d\'une autre organisation', function () {
    [$user] = userWithOrganization();
    $autreUsage = AiUsage::factory()->create();

    $this->actingAs($user)
        ->delete("/usages/{$autreUsage->id}")
        ->assertForbidden();

    $this->assertDatabaseHas('ai_usages', ['id' => $autreUsage->id]);
});
