<?php

declare(strict_types=1);

use App\Models\AiUsage;
use App\Models\Organization;
use App\Models\User;

it('redirige les invités vers la page de connexion', function () {
    $this->get('/dashboard')->assertRedirect('/login');
});

it('affiche le CTA de création d\'organisation si l\'utilisateur n\'a pas d\'org', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('/dashboard');

    $response->assertOk();
    $response->assertSee('Créer mon organisation', false);
});

it('affiche le nom de l\'organisation et la liste des usages quand elle existe', function () {
    $user = User::factory()->create();
    $organization = Organization::factory()->for($user)->create([
        'name' => 'Acme PME',
    ]);
    AiUsage::factory()->for($organization)->create(['name' => 'ChatGPT RH']);

    $response = $this->actingAs($user)->get('/dashboard');

    $response->assertOk();
    $response->assertSee('Acme PME');
    $response->assertSee('ChatGPT RH');
});

it('n\'affiche jamais les usages d\'une autre organisation sur le dashboard', function () {
    $user = User::factory()->create();
    $organization = Organization::factory()->for($user)->create();

    $autreUser = User::factory()->create();
    $autreOrg = Organization::factory()->for($autreUser)->create();
    AiUsage::factory()->for($autreOrg)->create(['name' => 'Usage secret de l\'autre PME']);

    $response = $this->actingAs($user)->get('/dashboard');

    $response->assertOk();
    $response->assertDontSee('Usage secret de l\'autre PME');
});
