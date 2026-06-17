<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Backfill : chaque organisation existante (créée à l'époque du modèle
     * 1 user = 1 organisation) donne une membership "owner" à son créateur, afin que
     * les comptes existants continuent de fonctionner à l'identique.
     */
    public function up(): void
    {
        $now = now();

        DB::table('organizations')
            ->whereNotNull('user_id')
            ->orderBy('id')
            ->chunkById(500, function ($organizations) use ($now) {
                $rows = $organizations->map(fn ($org) => [
                    'organization_id' => $org->id,
                    'user_id' => $org->user_id,
                    'role' => 'owner',
                    'created_at' => $now,
                    'updated_at' => $now,
                ])->all();

                // insertOrIgnore : idempotent si la migration est rejouée.
                DB::table('organization_user')->insertOrIgnore($rows);
            });
    }

    public function down(): void
    {
        // Ne retire que les memberships issues du backfill (créateur → owner).
        DB::table('organizations')
            ->whereNotNull('user_id')
            ->orderBy('id')
            ->chunkById(500, function ($organizations) {
                foreach ($organizations as $org) {
                    DB::table('organization_user')
                        ->where('organization_id', $org->id)
                        ->where('user_id', $org->user_id)
                        ->where('role', 'owner')
                        ->delete();
                }
            });
    }
};
