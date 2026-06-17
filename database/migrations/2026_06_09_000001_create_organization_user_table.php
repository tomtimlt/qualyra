<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Pivot des memberships : qui a accès à quelle organisation, avec quel rôle.
     *
     * Rôles (contextuels à l'organisation — un même user peut être owner de
     * sa propre organisation et auditor de plusieurs organisations clientes) :
     *   - owner   : administrateur côté client (l'organisation)
     *   - member  : collaborateur côté client
     *   - auditor : consultant du cabinet qui accompagne l'organisation
     */
    public function up(): void
    {
        Schema::create('organization_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('role', ['owner', 'member', 'auditor']);
            $table->timestamps();

            // Un user n'a qu'un seul rôle par organisation.
            $table->unique(['organization_id', 'user_id']);
            // Requête portefeuille : "toutes les orgs où je suis auditor".
            $table->index(['user_id', 'role']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('organization_user');
    }
};
