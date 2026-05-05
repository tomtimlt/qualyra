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
        Schema::create('ai_usages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('type', ['LLM_GEN', 'IA_GEN', 'IA_SCORING', 'IA_BIO', 'AUTRE']);
            $table->enum('domain', ['RH', 'EDUCATION', 'CREDIT', 'SANTE', 'SECURITE', 'MARKETING', 'PROD_INT', 'DEV_LOG', 'AUTRE']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_usages');
    }
};
