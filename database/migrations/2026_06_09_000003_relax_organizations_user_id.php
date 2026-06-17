<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Assouplit organizations.user_id : la contrainte 1 user = 1 organisation saute
     * (un auditeur peut créer N organisations clientes). La colonne est
     * conservée comme trace du créateur mais n'est PLUS JAMAIS utilisée pour
     * l'autorisation (source de vérité : pivot organization_user).
     *
     * Le cascade onDelete devient nullOnDelete : la suppression du compte
     * du créateur (ex : un auditeur qui part du cabinet) ne doit pas
     * détruire les données de l'organisation cliente.
     */
    public function up(): void
    {
        Schema::table('organizations', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropUnique(['user_id']);
        });

        Schema::table('organizations', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->change();
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('organizations', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });

        Schema::table('organizations', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable(false)->change();
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->unique(['user_id']);
        });
    }
};
