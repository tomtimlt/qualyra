<?php

declare(strict_types=1);

use App\Models\Organization;
use App\Models\User;

it('refuse l\'accès au formulaire de création aux invités', function () {
    $this->get('/organization/create')->assertRedirect('/login');
});

it('affiche le formulaire de création pour un utilisateur sans organisation', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get('/organization/create')
        ->assertOk()
        ->assertSee('Nom de l&#039;organisation', false);
});

it('redirige vers le dashboard si l\'utilisateur a déjà une organisation', function () {
    $user = User::factory()->create();
    Organization::factory()->for($user)->create();

    $this->actingAs($user)
        ->get('/organization/create')
        ->assertRedirect('/dashboard');
});

it('crée une organisation valide et la rattache au user courant', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post('/organization', [
        'name' => 'Ma PME SAS',
        'siret' => '12345678901234',
        'size' => '20-49',
        'sector' => 'Industrie',
    ]);

    $response->assertRedirect('/dashboard');

    $this->assertDatabaseHas('organizations', [
        'user_id' => $user->id,
        'name' => 'Ma PME SAS',
        'siret' => '12345678901234',
        'size' => '20-49',
        'sector' => 'Industrie',
    ]);
});

it('refuse de créer une seconde organisation pour le même user', function () {
    $user = User::factory()->create();
    Organization::factory()->for($user)->create();

    $response = $this->actingAs($user)->post('/organization', [
        'name' => 'Tentative de doublon',
        'size' => '1-19',
    ]);

    // FormRequest::authorize() renvoie false → 403
    $response->assertForbidden();
    expect($user->fresh()->organization()->count())->toBe(1);
});

it('ignore une tentative de mass assignment sur user_id', function () {
    $user = User::factory()->create();
    $autreUser = User::factory()->create();

    $this->actingAs($user)->post('/organization', [
        'name' => 'Tentative de takeover',
        'size' => '1-19',
        'user_id' => $autreUser->id, // doit être ignoré (hors $fillable)
    ])->assertRedirect('/dashboard');

    // L'organisation doit être rattachée au user authentifié, pas à autreUser
    $this->assertDatabaseHas('organizations', [
        'name' => 'Tentative de takeover',
        'user_id' => $user->id,
    ]);
    $this->assertDatabaseMissing('organizations', [
        'name' => 'Tentative de takeover',
        'user_id' => $autreUser->id,
    ]);
});

it('valide les champs requis et le format SIRET', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->post('/organization', [
        'name' => '',
        'siret' => 'pas-14-chiffres',
        'size' => 'taille-invalide',
    ])->assertSessionHasErrors(['name', 'siret', 'size']);
});
