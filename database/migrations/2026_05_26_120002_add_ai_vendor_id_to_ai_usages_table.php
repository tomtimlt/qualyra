<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ai_usages', function (Blueprint $table) {
            // Nullable : rattachement vendor optionnel (cas usage interne sans
            // fournisseur identifié, ou rétro-saisie progressive).
            // nullOnDelete : suppression d'un vendor ne casse pas l'usage,
            // il est juste détaché.
            $table->foreignId('ai_vendor_id')
                ->nullable()
                ->after('organization_id')
                ->constrained('ai_vendors')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('ai_usages', function (Blueprint $table) {
            $table->dropForeign(['ai_vendor_id']);
            $table->dropColumn('ai_vendor_id');
        });
    }
};
