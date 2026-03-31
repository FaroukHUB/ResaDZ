<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Prospect extends Model
{
    protected $fillable = [
        'nom',
        'telephone',
        'email',
        'wilaya',
        'nb_vehicules',
        'source',
        'statut',
        'notes',
        'date_dernier_contact',
        'relance_le',
        'loueur_id',
    ];

    protected $casts = [
        'date_dernier_contact' => 'date',
        'relance_le' => 'date',
        'nb_vehicules' => 'integer',
    ];

    const SOURCES = [
        'facebook' => 'Facebook',
        'google' => 'Google',
        'telegram' => 'Telegram',
        'terrain' => 'Terrain',
        'whatsapp' => 'WhatsApp',
        'autre' => 'Autre',
    ];

    const STATUTS = [
        'non_contacte' => 'Non contacté',
        'contacte' => 'Contacté',
        'interesse' => 'Intéressé',
        'inscrit' => 'Inscrit',
        'pas_interesse' => 'Pas intéressé',
    ];

    public function loueur(): BelongsTo
    {
        return $this->belongsTo(Loueur::class);
    }

    public function scopeParRelance($query)
    {
        return $query->whereNotNull('relance_le')->where('relance_le', '<=', now()->toDateString());
    }

    public function scopeParStatut($query, string $statut)
    {
        return $query->where('statut', $statut);
    }
}
