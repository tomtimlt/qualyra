<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Response extends Model
{
    use HasFactory;

    protected $fillable = [
        'variable_key',
        'variable_value',
    ];

    public function aiUsage(): BelongsTo
    {
        return $this->belongsTo(AiUsage::class);
    }
}
