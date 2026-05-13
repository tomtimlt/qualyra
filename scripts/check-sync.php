#!/usr/bin/env php
<?php
/**
 * scripts/check-sync.php — Analyse statique config ↔ seeder
 *
 * Vérifie que les IDs de règles dans config/ai_act_rules.php sont
 * référencés dans database/seeders/DemoSeeder.php.
 * Utilise les helpers Laravel (config, app) pour une analyse précise.
 */

declare(strict_types=1);

// Boot Laravel minimal pour accéder à config()
require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Extraire les IDs de règles depuis la config
$rules = config('ai_act_rules');
$configIds = [];

foreach ($rules as $key => $rule) {
    if (isset($rule['id'])) {
        $configIds[] = $rule['id'];
    } elseif (isset($rule['rules'])) {
        foreach ($rule['rules'] as $subRule) {
            if (isset($subRule['id'])) {
                $configIds[] = $subRule['id'];
            }
        }
    }
}

// Lire le DemoSeeder
$seederPath = __DIR__ . '/../database/seeders/DemoSeeder.php';
if (!file_exists($seederPath)) {
    echo "❌ DemoSeeder introuvable: $seederPath\n";
    exit(1);
}

$seederContent = file_get_contents($seederPath);

// Vérifier que chaque ID de règle est référencé dans le seeder
$missing = [];
foreach ($configIds as $id) {
    if (!str_contains($seederContent, $id)) {
        $missing[] = $id;
    }
}

if (count($missing) > 0) {
    echo "❌ DÉSYNCHRONISATION DÉTECTÉE\n";
    echo "   IDs de règles dans config/ai_act_rules.php mais ABSENTS de DemoSeeder.php:\n";
    foreach ($missing as $id) {
        echo "   - $id\n";
    }
    echo "\n   Action: ajouter des cas de test correspondants dans database/seeders/DemoSeeder.php\n";
    exit(1);
}

echo "✓ Synchronisation OK — " . count($configIds) . " IDs de règles vérifiés\n";
exit(0);
