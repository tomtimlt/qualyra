<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AiVendor extends Model
{
    use HasFactory;

    /**
     * organization_id volontairement absent : la FK est injectée par
     * Eloquent via la relation hasMany ($organization->aiVendors()->create(...)).
     * Garder cette colonne hors de $fillable empêche tout mass assignment
     * d'un vendor dans une autre organisation (isolation tenant stricte).
     */
    protected $fillable = [
        'name',
        'type_contractuel',
        'pays_hebergement',
        'hors_ue',
        'declaration_conformite_art47',
        'dpa_art28_signe',
        'cct_signees',
        'notes',
    ];

    protected $casts = [
        'hors_ue' => 'boolean',
        'declaration_conformite_art47' => 'boolean',
        'dpa_art28_signe' => 'boolean',
        'cct_signees' => 'boolean',
    ];

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function aiUsages(): HasMany
    {
        return $this->hasMany(AiUsage::class);
    }
}
