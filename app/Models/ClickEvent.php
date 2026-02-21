<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClickEvent extends Model
{
    protected $fillable = [
        'event_type',
        'vehicle_id',
        'loueur_id',
        'page_url',
        'ip_address',
        'session_id',
        'user_agent',
        'device_type',
        'country_code',
        'city',
        'metadata',
        'clicked_at',
    ];

    protected $casts = [
        'metadata' => 'array',
        'clicked_at' => 'datetime',
    ];

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function loueur(): BelongsTo
    {
        return $this->belongsTo(Loueur::class);
    }

    /**
     * Event types:
     * - phone_click: Click on phone number
     * - whatsapp_click: Click on WhatsApp button
     * - reserve_click: Click on reserve button
     * - compare_add: Add to comparison
     * - share_click: Share button click
     * - gallery_view: Photo gallery opened
     * - contact_form: Contact form submitted
     */
    public const TYPE_PHONE = 'phone_click';
    public const TYPE_WHATSAPP = 'whatsapp_click';
    public const TYPE_RESERVE = 'reserve_click';
    public const TYPE_COMPARE = 'compare_add';
    public const TYPE_SHARE = 'share_click';
    public const TYPE_GALLERY = 'gallery_view';
    public const TYPE_CONTACT = 'contact_form';

    /**
     * Scope for specific event type.
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('event_type', $type);
    }

    /**
     * Scope for phone clicks.
     */
    public function scopePhoneClicks($query)
    {
        return $query->where('event_type', self::TYPE_PHONE);
    }

    /**
     * Scope for WhatsApp clicks.
     */
    public function scopeWhatsAppClicks($query)
    {
        return $query->where('event_type', self::TYPE_WHATSAPP);
    }

    /**
     * Scope for reserve clicks.
     */
    public function scopeReserveClicks($query)
    {
        return $query->where('event_type', self::TYPE_RESERVE);
    }
}
