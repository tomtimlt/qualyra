<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AiUsage extends Model
{
    use HasFactory;

    /**
     * organization_id volontairement absent : la FK est injectée par
     * Eloquent via la relation hasMany ($organization->aiUsages()->create(...)).
     * Garder cette colonne hors de $fillable empêche tout mass assignment
     * d'un usage dans une autre organisation (isolation tenant stricte).
     */
    protected $fillable = [
        'name',
        'description',
        'type',
        'domain',
    ];

    protected $casts = [
        'type' => 'string',
        'domain' => 'string',
    ];

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function responses(): HasMany
    {
        return $this->hasMany(Response::class);
    }

    public function assessments(): HasMany
    {
        return $this->hasMany(Assessment::class);
    }
}
