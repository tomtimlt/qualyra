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
        Schema::create('organizations', function (Blueprint $table) {
            $table->id();
            // 1 user = 1 organisation : contrainte BDD pour bloquer un éventuel
            // second INSERT même si la couche applicative est contournée.
            $table->foreignId('user_id')->unique()->constrained()->onDelete('cascade');
            $table->string('name');
            // Le SIRET identifie légalement une organisation : doit être unique
            // si renseigné. nullable() autorise les enregistrements sans SIRET.
            $table->string('siret')->nullable()->unique();
            $table->enum('size', ['1-19', '20-49', '50-149', '150+']);
            $table->string('sector')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('organizations');
    }
};
