<?php

declare(strict_types=1);

use App\Models\AiUsage;
use App\Models\Organization;
use App\Models\Report;
use App\Models\User;
use App\Services\ReportSnapshotBuilder;

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

it('télécharge le PDF d\'un report même sans paiement', function () {
    [$user, $organization] = userWithOrgAndUsage();
    $report = $organization->reports()->create([
        'snapshot' => ['organization' => ['name' => 'Acme'], 'usages' => [], 'summary' => [], 'generated_at' => now()->toIso8601String()],
    ]);

    $this->actingAs($user)
        ->get(route('reports.download', $report))
        ->assertOk();
});

// ---------------------------------------------------------------------------
// SÉCURITÉ : isolation tenant sur les reports
// ---------------------------------------------------------------------------

it('renvoie 403 si un user accède à un report d\'une autre organisation', function () {
    [$user] = userWithOrgAndUsage();

    $autreUser = User::factory()->create();
    $autreOrg = Organization::factory()->for($autreUser)->create();
    $autreReport = $autreOrg->reports()->create([
        'snapshot' => ['organization' => ['name' => 'Autre Organisation'], 'usages' => [], 'summary' => [], 'generated_at' => now()->toIso8601String()],
        'paid_at' => now(),
    ]);

    $this->actingAs($user)->get(route('reports.show', $autreReport))->assertForbidden();
    $this->actingAs($user)->get(route('reports.download', $autreReport))->assertForbidden();
});

// ---------------------------------------------------------------------------
// Contenu rédactionnel : encadrés conditionnels et figement du snapshot
// ---------------------------------------------------------------------------

/**
 * Helper : crée un rapport contenant un usage configuré, en passant par le
 * SnapshotBuilder pour obtenir un snapshot.content figé identique à celui
 * d'un rapport généré via /checkout.
 *
 * @param  array<string, string>  $responses
 * @param  array<string, mixed>|null  $assessmentOverride  ex: niveau, regle_id, alertes
 */
function reportWithUsage(string $type, string $domain, array $responses = [], ?array $assessmentOverride = null): Report
{
    $user = User::factory()->create();
    $organization = Organization::factory()->for($user)->create();
    $usage = AiUsage::factory()->for($organization)->create([
        'type' => $type,
        'domain' => $domain,
    ]);
    foreach ($responses as $key => $value) {
        $usage->responses()->create(['variable_key' => $key, 'variable_value' => $value]);
    }
    if ($assessmentOverride !== null) {
        $usage->assessments()->create(array_merge([
            'niveau' => 'RISQUE_MINIMAL',
            'regle_id' => 'DEFAULT',
            'article' => 'N/A',
            'raison' => 'Test',
            'alertes' => [],
            'type_regle' => 'NA',
            'computed_at' => now(),
        ], $assessmentOverride));
    }

    $snapshot = app(ReportSnapshotBuilder::class)->build($organization->fresh());

    return $organization->reports()->create([
        'snapshot' => $snapshot,
        'paid_at' => now(),
    ]);
}

it('snapshot contient bien le contenu rédactionnel résolu (clé content)', function () {
    $report = reportWithUsage('LLM_GEN', 'PROD_INT', ['finality' => 'OK']);

    expect($report->snapshot)->toHaveKey('content');
    expect($report->snapshot['content'])->toHaveKey('introduction');
    expect($report->snapshot['content'])->toHaveKey('synthese_executive');
    expect($report->snapshot['content'])->toHaveKey('usages');
    expect($report->snapshot['content'])->toHaveKey('plan_action');
    expect($report->snapshot['content'])->toHaveKey('checklist');
    expect($report->snapshot['content'])->toHaveKey('disclaimer');
    expect($report->snapshot['content']['meta']['nom_pme'])->toBeString();
});

it('encadré "Article 22 human-washing" présent si une alerte rgpd_art22 a été émise', function () {
    $report = reportWithUsage('AUTRE', 'AUTRE', ['data_personal' => 'yes', 'human_oversight' => 'never'], [
        'niveau' => 'HAUT_RISQUE',
        'regle_id' => 'R-H-02',
        'article' => 'Annexe III §4 a)',
        'raison' => 'Test HR',
        'alertes' => [['code' => 'rgpd_art22', 'message' => 'Décision auto', 'type' => 'AGGRAVATION']],
        'type_regle' => 'TEXTE_EXPLICITE',
    ]);

    $usage = $report->snapshot['content']['usages'][0];
    $titres = collect($usage['encadres'])->pluck('titre')->all();

    expect(implode(' | ', $titres))->toContain('Human-washing');
});

it('encadré "Information des travailleurs" absent si aucun usage haut risque + employes', function () {
    $report = reportWithUsage('LLM_GEN', 'PROD_INT', ['pub' => 'AUCUN'], [
        'niveau' => 'RISQUE_MINIMAL',
        'regle_id' => 'DEFAULT',
        'article' => 'N/A',
        'raison' => 'Test minimal',
        'alertes' => [],
        'type_regle' => 'NA',
    ]);

    $usage = $report->snapshot['content']['usages'][0];
    $titres = collect($usage['encadres'])->pluck('titre')->all();

    expect(implode(' | ', $titres))->not->toContain('Information des travailleurs');
});

it('encadré "Information des travailleurs" présent si haut risque + pub contient EMPLOYES', function () {
    $report = reportWithUsage('IA_SCORING', 'RH', ['pub' => 'EMPLOYES,CLIENTS', 'rh_usage' => 'TRI_CV'], [
        'niveau' => 'HAUT_RISQUE',
        'regle_id' => 'R-H-02',
        'article' => 'Annexe III §4 a)',
        'raison' => 'Test',
        'alertes' => [],
        'type_regle' => 'TEXTE_EXPLICITE',
    ]);

    $usage = $report->snapshot['content']['usages'][0];
    $titres = collect($usage['encadres'])->pluck('titre')->all();

    expect(implode(' | ', $titres))->toContain('Information des travailleurs');
});

it('encadré "Transparence chatbot" déclenché par regle_id R-L-01', function () {
    $report = reportWithUsage('LLM_GEN', 'MARKETING', ['interaction_directe' => 'OUI'], [
        'niveau' => 'RISQUE_LIMITE',
        'regle_id' => 'R-L-01',
        'article' => 'Art. 50 §1',
        'raison' => 'Chatbot',
        'alertes' => [],
        'type_regle' => 'TEXTE_EXPLICITE',
    ]);

    $usage = $report->snapshot['content']['usages'][0];
    $titres = collect($usage['encadres'])->pluck('titre')->all();

    expect(implode(' | ', $titres))->toContain('Transparence chatbot');
});

it('encadré "Transferts hors UE / DPF" déclenché si fournisseur LLM US', function () {
    $report = reportWithUsage('LLM_GEN', 'PROD_INT', ['llm_provider' => 'openai']);

    $usage = $report->snapshot['content']['usages'][0];
    $titres = collect($usage['encadres'])->pluck('titre')->all();

    expect(implode(' | ', $titres))->toContain('Data Privacy Framework');
});

it('le snapshot reste figé même après modification des usages sous-jacents', function () {
    $user = User::factory()->create();
    $organization = Organization::factory()->for($user)->create();
    $usage = AiUsage::factory()->for($organization)->create([
        'name' => 'Nom initial',
        'type' => 'LLM_GEN',
        'domain' => 'PROD_INT',
    ]);
    $usage->responses()->create(['variable_key' => 'finality', 'variable_value' => 'OK']);

    $snapshot = app(ReportSnapshotBuilder::class)->build($organization->fresh());
    $report = $organization->reports()->create(['snapshot' => $snapshot, 'paid_at' => now()]);

    // Mutation post-génération : on change le nom et on supprime un usage.
    $usage->update(['name' => 'Nom modifié après génération']);

    $reread = $report->fresh();
    $usageInSnapshot = $reread->snapshot['content']['usages'][0];

    expect($usageInSnapshot['name'])->toBe('Nom initial');
});

it('le PDF rendu ne contient aucun placeholder {variable} non substitué', function () {
    [$user, $organization] = userWithOrgAndUsage();
    config()->set('services.stripe.secret', null);

    // Un usage RH HAUT_RISQUE avec rh_usage pour activer l'encadré CSE.
    $organization->aiUsages()->first()->update(['domain' => 'RH']);
    $organization->aiUsages()->first()->responses()->createMany([
        ['variable_key' => 'pub', 'variable_value' => 'EMPLOYES'],
        ['variable_key' => 'dec', 'variable_value' => 'AIDE_DEC'],
        ['variable_key' => 'rh_usage', 'variable_value' => 'TRI_CV'],
    ]);

    $this->actingAs($user)->post('/checkout');
    $report = $organization->reports()->latest()->first();

    $response = $this->actingAs($user)->get(route('reports.download', $report));
    $response->assertOk();

    // Le PDF est binaire ; on régénère le HTML brut (avant rendu DomPDF) pour
    // détecter les placeholders manquants — DomPDF ne perd pas le texte.
    $html = view('reports.pdf', ['report' => $report])->render();

    // Aucun {nom_pme}, {date_audit}, {nb_usages_*}, etc. ne doit subsister.
    expect($html)->not->toMatch('/\{[a-z_]+\}/');
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
        'snapshot' => ['organization' => ['name' => 'Autre Organisation'], 'usages' => [], 'summary' => [], 'generated_at' => now()->toIso8601String()],
        'paid_at' => now(),
    ]);

    $response = $this->actingAs($user)->get('/reports');

    $response->assertOk();
    $response->assertSee('Rapport #'.$organization->reports->first()->id);
    $response->assertDontSee('Rapport #'.$autreOrg->reports->first()->id);
});
