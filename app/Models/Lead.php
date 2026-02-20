<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    protected $fillable = [
        'email',
        'phone',
        'name',
        'source',
        'source_page',
        'interests',
        'subscribed_newsletter',
        'subscribed_whatsapp',
        'subscribed_telegram',
        'subscribed_sms',
        'ip_address',
        'user_agent',
        'utm_source',
        'utm_medium',
        'utm_campaign',
    ];

    protected $casts = [
        'interests' => 'array',
        'subscribed_newsletter' => 'boolean',
        'subscribed_whatsapp' => 'boolean',
        'subscribed_telegram' => 'boolean',
        'subscribed_sms' => 'boolean',
    ];

    public function scopeWithEmail($query)
    {
        return $query->whereNotNull('email');
    }

    public function scopeWithPhone($query)
    {
        return $query->whereNotNull('phone');
    }

    public function scopeSubscribedTo($query, string $channel)
    {
        return $query->where("subscribed_{$channel}", true);
    }

    public function scopeFromSource($query, string $source)
    {
        return $query->where('source', $source);
    }

    public static function capture(array $data): self
    {
        $data['ip_address'] = $data['ip_address'] ?? request()->ip();
        $data['user_agent'] = $data['user_agent'] ?? request()->userAgent();
        $data['source_page'] = $data['source_page'] ?? request()->fullUrl();

        // Extract UTM parameters
        $data['utm_source'] = $data['utm_source'] ?? request()->get('utm_source');
        $data['utm_medium'] = $data['utm_medium'] ?? request()->get('utm_medium');
        $data['utm_campaign'] = $data['utm_campaign'] ?? request()->get('utm_campaign');

        return static::create($data);
    }
}
