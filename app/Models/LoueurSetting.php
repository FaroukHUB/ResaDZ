<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoueurSetting extends Model
{
    protected $fillable = [
        'loueur_id',
        'key',
        'value',
        'type',
    ];

    public function loueur(): BelongsTo
    {
        return $this->belongsTo(Loueur::class);
    }

    public function getTypedValue()
    {
        return match ($this->type) {
            'boolean' => (bool) $this->value,
            'integer' => (int) $this->value,
            'decimal' => (float) $this->value,
            'json' => json_decode($this->value, true),
            default => $this->value,
        };
    }
}
