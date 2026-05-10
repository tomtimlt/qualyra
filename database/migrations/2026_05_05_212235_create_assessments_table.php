<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ai_usage_id')->constrained()->onDelete('cascade');
            $table->enum('niveau', ['INACCEPTABLE', 'HAUT_RISQUE', 'RISQUE_LIMITE', 'RISQUE_MINIMAL']);
            $table->string('regle_id');
            $table->string('article');
            $table->text('raison');
            $table->json('alertes');
            $table->enum('type_regle', ['TEXTE_EXPLICITE', 'INTERPRETATION', 'NA']);
            $table->timestamp('computed_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assessments');
    }
};
