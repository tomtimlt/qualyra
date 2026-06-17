<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Organization extends Model
{
    use HasFactory;

    /**
     * user_id volontairement absent : la FK est injectée par Eloquent
     * via la relation hasOne ($user->organization()->create(...)).
     * Empêche un POST malicieux de rattacher l'organisation à un autre
     * compte utilisateur (mass assignment / takeover).
     */
    protected $fillable = [
        'name',
        'siret',
        'size',
        'sector',
    ];

    protected $casts = [
        'size' => 'string',
    ];

    /**
     * Créateur de l'organisation (trace historique). N'est PLUS utilisé pour
     * l'autorisation : la source de vérité est le pivot organization_user.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Membres de l'organisation avec leur rôle (owner / member / auditor).
     */
    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->withPivot('role')
            ->withTimestamps();
    }

    public function aiUsages(): HasMany
    {
        return $this->hasMany(AiUsage::class);
    }

    public function aiVendors(): HasMany
    {
        return $this->hasMany(AiVendor::class);
    }

    public function reports(): HasMany
    {
        return $this->hasMany(Report::class);
    }
}
