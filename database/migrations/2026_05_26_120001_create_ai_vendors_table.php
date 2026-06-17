<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_vendors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            // type_contractuel : INTERNE (auto-hébergé), SAAS (multi-tenant),
            // API_PUBLIC (clé API), OPEN_SOURCE (modèle exécuté localement).
            $table->enum('type_contractuel', ['INTERNE', 'SAAS', 'API_PUBLIC', 'OPEN_SOURCE']);
            // Pays d'hébergement principal des données traitées (code ISO-3166 alpha-2).
            $table->string('pays_hebergement', 2)->nullable();
            $table->boolean('hors_ue')->default(false);
            // Déclaration de conformité du fournisseur au titre de l'Art. 47 AI Act
            // (obligatoire pour les systèmes haut risque mis sur le marché UE).
            $table->boolean('declaration_conformite_art47')->default(false);
            // Contrat de sous-traitance RGPD Art. 28 signé avec le fournisseur.
            $table->boolean('dpa_art28_signe')->default(false);
            // Clauses contractuelles types (Décision UE 2021/914) signées —
            // nullable car non pertinent si fournisseur dans l'UE.
            $table->boolean('cct_signees')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_vendors');
    }
};
