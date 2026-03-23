<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'group',
        'key',
        'value',
        'type',
        'label',
        'description',
        'sort_order',
    ];

    // Récupérer une valeur de setting
    public static function get(string $key, $default = null)
    {
        try {
            $setting = Cache::remember("setting.{$key}", 3600, function () use ($key) {
                return self::where('key', $key)->first();
            });

            if (!$setting) {
                return $default;
            }

            return self::castValue($setting->value, $setting->type);
        } catch (\Exception $e) {
            // Handle case when database/cache tables don't exist yet (during migrations)
            return $default;
        }
    }

    // Définir une valeur de setting
    public static function set(string $key, $value, string $group = 'general', string $type = 'text'): void
    {
        self::updateOrCreate(
            ['key' => $key],
            [
                'value' => is_array($value) ? json_encode($value) : $value,
                'group' => $group,
                'type' => $type,
            ]
        );

        Cache::forget("setting.{$key}");
    }

    // Récupérer tous les settings d'un groupe
    public static function getGroup(string $group): array
    {
        $settings = self::where('group', $group)->get();

        $result = [];
        foreach ($settings as $setting) {
            $result[$setting->key] = self::castValue($setting->value, $setting->type);
        }

        return $result;
    }

    // Cast la valeur selon le type
    protected static function castValue($value, string $type)
    {
        return match($type) {
            'boolean' => filter_var($value, FILTER_VALIDATE_BOOLEAN),
            'number' => (float) $value,
            'json' => json_decode($value, true),
            default => $value,
        };
    }

    public function scopeByGroup($query, string $group)
    {
        return $query->where('group', $group);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('group')->orderBy('sort_order')->orderBy('key');
    }
}
