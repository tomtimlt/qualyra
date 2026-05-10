<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('responses', function (Blueprint $table) {
            // Une seule réponse par (usage IA, clé de variable) — permet l'upsert
            // depuis le contrôleur sans accumulation d'historique.
            $table->unique(['ai_usage_id', 'variable_key'], 'responses_usage_key_unique');
        });
    }

    public function down(): void
    {
        Schema::table('responses', function (Blueprint $table) {
            $table->dropUnique('responses_usage_key_unique');
        });
    }
};
