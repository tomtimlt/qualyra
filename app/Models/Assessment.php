<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Assessment extends Model
{
    use HasFactory;

    protected $fillable = [
        'niveau',
        'regle_id',
        'article',
        'raison',
        'alertes',
        'type_regle',
        'computed_at',
    ];

    protected $casts = [
        'niveau' => 'string',
        'alertes' => 'array',
        'type_regle' => 'string',
        'computed_at' => 'datetime',
    ];

    public function aiUsage(): BelongsTo
    {
        return $this->belongsTo(AiUsage::class);
    }
}
