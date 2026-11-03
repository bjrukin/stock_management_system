<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

class SettingService
{
    protected const CACHE_PREFIX = 'pharma_setting_';
    protected const CACHE_TTL = 3600; // 1 hour

    /**
     * Get a setting value by key with optional fallback.
     */
    public static function get(string $key, $default = null)
    {
        return Cache::remember(self::CACHE_PREFIX . $key, self::CACHE_TTL, function () use ($key, $default) {
            $setting = Setting::where('key', $key)->first();
            if (! $setting) {
                return $default;
            }

            return $setting->typed_value ?? $default;
        });
    }

    /**
     * Set/persist a setting value and refresh cache.
     */
    public static function set(string $key, $value, string $group = 'general', string $type = 'string', ?string $description = null): Setting
    {
        $stringValue = is_array($value) ? json_encode($value) : (string) $value;

        $setting = Setting::updateOrCreate(
            ['key' => $key],
            [
                'value' => $stringValue,
                'group' => $group,
                'type' => $type,
                'description' => $description,
            ]
        );

        Cache::forget(self::CACHE_PREFIX . $key);

        return $setting;
    }

    /**
     * Get all settings grouped by group.
     */
    public static function allByGroup(string $group): array
    {
        return Setting::where('group', $group)
            ->get()
            ->mapWithKeys(fn (Setting $s) => [$s->key => $s->typed_value])
            ->toArray();
    }

    /**
     * Clear all cached settings.
     */
    public static function clearCache(): void
    {
        $keys = Setting::pluck('key');
        foreach ($keys as $key) {
            Cache::forget(self::CACHE_PREFIX . $key);
        }
    }
}
