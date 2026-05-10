<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Report extends Model
{
    use HasFactory;

    /**
     * organization_id volontairement absent : injecté via la relation
     * (cohérent avec AiUsage et Response — empêche tout mass assignment cross-tenant).
     */
    protected $fillable = [
        'snapshot',
        'stripe_session_id',
        'paid_at',
    ];

    protected $casts = [
        'snapshot' => 'array',
        'paid_at' => 'datetime',
    ];

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function isPaid(): bool
    {
        return $this->paid_at !== null;
    }

    public function scopePaid(Builder $query): Builder
    {
        return $query->whereNotNull('paid_at');
    }
}
