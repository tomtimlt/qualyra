<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function aiUsages(): HasMany
    {
        return $this->hasMany(AiUsage::class);
    }
}
