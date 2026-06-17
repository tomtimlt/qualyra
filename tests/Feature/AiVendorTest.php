<?php

declare(strict_types=1);

use App\Models\AiUsage;
use App\Models\AiVendor;
use App\Models\Organization;
use App\Models\User;

/**
 * Helper local : user + organisation rattachée.
 */
function userWithOrgForVendor(): array
{
    $user = User::factory()->create();
    $organization = Organization::factory()->for($user)->create();

    return [$user, $organization];
}

// ---------------------------------------------------------------------------
// Accès anonyme (guest)
// ---------------------------------------------------------------------------

it('refuse toutes les routes vendors aux invités', function () {
    $vendor = AiVendor::factory()->create();

    $this->get('/vendors')->assertRedirect('/login');
    $this->get('/vendors/create')->assertRedirect('/login');
    $this->get("/vendors/{$vendor->id}")->assertRedirect('/login');
    $this->get("/vendors/{$vendor->id}/edit")->assertRedirect('/login');
    $this->post('/vendors', [])->assertRedirect('/login');
    $this->patch("/vendors/{$vendor->id}", [])->assertRedirect('/login');
    $this->delete("/vendors/{$vendor->id}")->assertRedirect('/login');
});

// ---------------------------------------------------------------------------
// Pré-requis : avoir une organisation
// ---------------------------------------------------------------------------

it('redirige vers la création d\'organisation si pas d\'org (index)', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->get('/vendors')->assertRedirect(route('organization.create'));
});

it('refuse la création de vendor sans organisation', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post('/vendors', ['name' => 'X', 'type_contractuel' => 'INTERNE'])
        ->assertForbidden();
});

// ---------------------------------------------------------------------------
// CRUD
// ---------------------------------------------------------------------------

it('crée un vendor scopé à l\'organisation du user (organization_id forcé)', function () {
    [$user, $organization] = userWithOrgForVendor();

    $this->actingAs($user)
        ->post('/vendors', [
            'name' => 'Acme AI',
            'type_contractuel' => 'SAAS',
            'pays_hebergement' => 'FR',
        ])
        ->assertRedirect(route('vendors.index'));

    $vendor = AiVendor::firstWhere('name', 'Acme AI');
    expect($vendor)->not->toBeNull();
    expect($vendor->organization_id)->toBe($organization->id);
});

it('ignore organization_id soumis en POST (jamais en $fillable)', function () {
    [$user, $organization] = userWithOrgForVendor();
    [, $otherOrg] = userWithOrgForVendor();

    $this->actingAs($user)
        ->post('/vendors', [
            'name' => 'Acme AI',
            'type_contractuel' => 'SAAS',
            'organization_id' => $otherOrg->id, // tentative cross-tenant
        ])
        ->assertRedirect();

    $vendor = AiVendor::firstWhere('name', 'Acme AI');
    // L'organization_id reste celui du user authentifié, pas otherOrg.
    expect($vendor->organization_id)->toBe($organization->id);
});

it('refuse la mise à jour d\'un vendor d\'une autre organisation (IDOR)', function () {
    [$user] = userWithOrgForVendor();
    [, $otherOrg] = userWithOrgForVendor();
    $foreignVendor = AiVendor::factory()->for($otherOrg)->create();

    $this->actingAs($user)
        ->patch("/vendors/{$foreignVendor->id}", [
            'name' => 'pwned',
            'type_contractuel' => 'INTERNE',
        ])
        ->assertForbidden();
});

it('refuse la suppression d\'un vendor d\'une autre organisation (IDOR)', function () {
    [$user] = userWithOrgForVendor();
    [, $otherOrg] = userWithOrgForVendor();
    $foreignVendor = AiVendor::factory()->for($otherOrg)->create();

    $this->actingAs($user)
        ->delete("/vendors/{$foreignVendor->id}")
        ->assertForbidden();
    expect(AiVendor::find($foreignVendor->id))->not->toBeNull();
});

it('refuse l\'affichage d\'un vendor d\'une autre organisation (IDOR)', function () {
    [$user] = userWithOrgForVendor();
    [, $otherOrg] = userWithOrgForVendor();
    $foreignVendor = AiVendor::factory()->for($otherOrg)->create();

    $this->actingAs($user)
        ->get("/vendors/{$foreignVendor->id}")
        ->assertForbidden();
});

// ---------------------------------------------------------------------------
// Liaison AiUsage → AiVendor (sécurité tenant)
// ---------------------------------------------------------------------------

it('rattache un usage à un vendor de la même organisation via associate()', function () {
    [$user, $organization] = userWithOrgForVendor();
    $vendor = AiVendor::factory()->for($organization)->create();

    $this->actingAs($user)
        ->post('/usages', [
            'name' => 'ChatGPT',
            'type' => 'LLM_GEN',
            'domain' => 'PROD_INT',
            'ai_vendor_id' => $vendor->id,
        ])
        ->assertRedirect(route('usages.index'));

    $usage = AiUsage::firstWhere('name', 'ChatGPT');
    expect($usage->ai_vendor_id)->toBe($vendor->id);
});

it('refuse le rattachement d\'un usage à un vendor d\'une autre organisation (validation Form Request)', function () {
    [$user, $organization] = userWithOrgForVendor();
    [, $otherOrg] = userWithOrgForVendor();
    $foreignVendor = AiVendor::factory()->for($otherOrg)->create();

    $this->actingAs($user)
        ->post('/usages', [
            'name' => 'ChatGPT',
            'type' => 'LLM_GEN',
            'domain' => 'PROD_INT',
            'ai_vendor_id' => $foreignVendor->id,
        ])
        ->assertSessionHasErrors('ai_vendor_id');

    expect(AiUsage::where('name', 'ChatGPT')->exists())->toBeFalse();
});
