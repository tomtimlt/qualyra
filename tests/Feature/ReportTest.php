<?php

declare(strict_types=1);

use App\Models\AiUsage;
use App\Models\Organization;
use App\Models\User;

function userWithOrgAndUsage(): array
{
    $user = User::factory()->create();
    $organization = Organization::factory()->for($user)->create();
    $aiUsage = AiUsage::factory()->for($organization)->create([
        'type' => 'LLM_GEN',
        'domain' => 'PROD_INT',
    ]);

    return [$user, $organization, $aiUsage];
}

// ---------------------------------------------------------------------------
// Accès anonyme
// ---------------------------------------------------------------------------

it('refuse les routes reports/checkout aux invités', function () {
    $organization = Organization::factory()->for(User::factory())->create();
    $report = $organization->reports()->create([
        'snapshot' => ['organization' => ['name' => 'X'], 'usages' => [], 'summary' => [], 'generated_at' => now()->toIso8601String()],
        'paid_at' => now(),
    ]);

    $this->get('/reports')->assertRedirect('/login');
    $this->get("/reports/{$report->id}")->assertRedirect('/login');
    $this->get("/reports/{$report->id}/download")->assertRedirect('/login');
    $this->post('/checkout', [])->assertRedirect('/login');
});

// ---------------------------------------------------------------------------
// Génération via /checkout (mode dev sans Stripe configuré)
// ---------------------------------------------------------------------------

it('redirige vers organization.create si le user n\'a pas d\'organisation', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post('/checkout')
        ->assertRedirect(route('organization.create'));
});

it('refuse la génération si l\'organisation n\'a aucun usage IA', function () {
    $user = User::factory()->create();
    Organization::factory()->for($user)->create();

    $this->actingAs($user)
        ->post('/checkout')
        ->assertRedirect(route('usages.index'));
});

it('crée un Report et le marque payé en mode dev (STRIPE_SECRET vide)', function () {
    config()->set('services.stripe.secret', null);

    [$user, $organization] = userWithOrgAndUsage();

    $this->actingAs($user)
        ->post('/checkout')
        ->assertRedirectContains('/reports/');

    $report = $organization->reports()->latest()->first();
    expect($report)->not->toBeNull();
    expect($report->isPaid())->toBeTrue();
    expect($report->snapshot)->toHaveKey('organization');
    expect($report->snapshot['organization']['name'])->toBe($organization->name);
});

it('capture un snapshot complet (org + usages + assessments)', function () {
    config()->set('services.stripe.secret', null);

    [$user, $organization, $aiUsage] = userWithOrgAndUsage();
    $aiUsage->responses()->create(['variable_key' => 'finality', 'variable_value' => 'OK']);

    $this->actingAs($user)->post('/checkout');

    $report = $organization->reports()->latest()->first();
    expect($report->snapshot['usages'])->toHaveCount(1);
    expect($report->snapshot['usages'][0]['name'])->toBe($aiUsage->name);
    expect($report->snapshot['usages'][0]['responses'])->toHaveKey('finality');
});

// ---------------------------------------------------------------------------
// Téléchargement PDF
// ---------------------------------------------------------------------------

it('télécharge le PDF d\'un report payé', function () {
    [$user, $organization] = userWithOrgAndUsage();
    $report = $organization->reports()->create([
        'snapshot' => ['organization' => ['name' => 'Acme'], 'usages' => [], 'summary' => [], 'generated_at' => now()->toIso8601String()],
        'paid_at' => now(),
    ]);

    $response = $this->actingAs($user)->get(route('reports.download', $report));

    $response->assertOk();
    expect($response->headers->get('content-type'))->toContain('application/pdf');
});

it('refuse le téléchargement d\'un report non payé (HTTP 402)', function () {
    [$user, $organization] = userWithOrgAndUsage();
    $report = $organization->reports()->create([
        'snapshot' => ['organization' => ['name' => 'Acme'], 'usages' => [], 'summary' => [], 'generated_at' => now()->toIso8601String()],
    ]);

    $this->actingAs($user)
        ->get(route('reports.download', $report))
        ->assertStatus(402);
});

// ---------------------------------------------------------------------------
// SÉCURITÉ : isolation tenant sur les reports
// ---------------------------------------------------------------------------

it('renvoie 403 si un user accède à un report d\'une autre organisation', function () {
    [$user] = userWithOrgAndUsage();

    $autreUser = User::factory()->create();
    $autreOrg = Organization::factory()->for($autreUser)->create();
    $autreReport = $autreOrg->reports()->create([
        'snapshot' => ['organization' => ['name' => 'Autre PME'], 'usages' => [], 'summary' => [], 'generated_at' => now()->toIso8601String()],
        'paid_at' => now(),
    ]);

    $this->actingAs($user)->get(route('reports.show', $autreReport))->assertForbidden();
    $this->actingAs($user)->get(route('reports.download', $autreReport))->assertForbidden();
});

it('liste uniquement les reports de l\'organisation du user', function () {
    [$user, $organization] = userWithOrgAndUsage();
    $organization->reports()->create([
        'snapshot' => ['organization' => ['name' => 'Mienne'], 'usages' => [], 'summary' => [], 'generated_at' => now()->toIso8601String()],
        'paid_at' => now(),
    ]);

    $autreUser = User::factory()->create();
    $autreOrg = Organization::factory()->for($autreUser)->create();
    $autreOrg->reports()->create([
        'snapshot' => ['organization' => ['name' => 'Autre PME'], 'usages' => [], 'summary' => [], 'generated_at' => now()->toIso8601String()],
        'paid_at' => now(),
    ]);

    $response = $this->actingAs($user)->get('/reports');

    $response->assertOk();
    $response->assertSee('Rapport #'.$organization->reports->first()->id);
    $response->assertDontSee('Rapport #'.$autreOrg->reports->first()->id);
});
