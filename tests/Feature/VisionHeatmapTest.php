<?php

declare(strict_types=1);

use App\Models\AiUsage;
use App\Models\Assessment;
use App\Models\Organization;
use App\Models\User;

it('affiche l\'onglet Heatmap densité avec son canvas sur /vision', function () {
    $user = User::factory()->create();
    $organization = Organization::factory()->for($user)->create();

    $usage = AiUsage::factory()->for($organization)->create(['domain' => 'RH', 'name' => 'ChatGPT RH']);
    Assessment::factory()->for($usage)->create(['niveau' => 'HAUT_RISQUE']);

    $response = $this->actingAs($user)->get('/vision');

    $response->assertOk();
    $response->assertSee('Heatmap');
    $response->assertSee('id="chartHeatmapDensity"', false);
    $response->assertSee('ChatGPT RH'); // visible dans la liste filtrable
});

it('isole les usages par tenant dans la heatmap densité', function () {
    $userA = User::factory()->create();
    $orgA = Organization::factory()->for($userA)->create();
    $usageA = AiUsage::factory()->for($orgA)->create(['domain' => 'RH', 'name' => 'Usage Alpha']);
    Assessment::factory()->for($usageA)->create(['niveau' => 'HAUT_RISQUE']);

    $userB = User::factory()->create();
    $orgB = Organization::factory()->for($userB)->create();
    $usageB = AiUsage::factory()->for($orgB)->create(['domain' => 'CREDIT', 'name' => 'Usage Beta']);
    Assessment::factory()->for($usageB)->create(['niveau' => 'INACCEPTABLE']);

    $response = $this->actingAs($userA)->get('/vision');

    $response->assertOk();
    $response->assertSee('Usage Alpha');
    $response->assertDontSee('Usage Beta'); // étanchéité tenant
});
