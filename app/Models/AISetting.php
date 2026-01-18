<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;

class AISetting extends Model
{
    protected $table = 'ai_settings';

    protected $fillable = ['key', 'value', 'type', 'description'];

    /**
     * Keys that should be stored encrypted.
     */
    protected static array $encryptedKeys = [
        'anthropic_api_key',
        'openai_api_key',
        'google_api_key',
        'deepseek_api_key',
    ];

    /**
     * Check if a key should be encrypted.
     */
    public static function isEncryptedKey(string $key): bool
    {
        return in_array($key, self::$encryptedKeys);
    }

    /**
     * Get a setting value by key.
     */
    public static function getValue(string $key, mixed $default = null): mixed
    {
        $setting = Cache::remember("ai_setting_{$key}", 3600, function () use ($key) {
            return self::where('key', $key)->first();
        });

        if (!$setting) {
            return $default;
        }

        $value = $setting->value;

        // Decrypt if this is an encrypted key
        if (self::isEncryptedKey($key) && $value) {
            try {
                $value = Crypt::decryptString($value);
            } catch (\Exception $e) {
                // If decryption fails, value might be unencrypted (legacy)
                // Return as-is
            }
        }

        return self::castValue($value, $setting->type);
    }

    /**
     * Set a setting value.
     */
    public static function setValue(string $key, mixed $value, ?string $type = null): void
    {
        // Auto-detect type if not provided
        if ($type === null) {
            $type = match (true) {
                is_bool($value) => 'boolean',
                is_int($value) => 'integer',
                is_float($value) => 'float',
                is_array($value) => 'json',
                default => 'string',
            };
        }

        // Convert value to string for storage
        $storedValue = match ($type) {
            'boolean' => $value ? '1' : '0',
            'json' => json_encode($value),
            default => (string) $value,
        };

        // Encrypt if this is a sensitive key
        if (self::isEncryptedKey($key) && $storedValue) {
            $storedValue = Crypt::encryptString($storedValue);
        }

        self::updateOrCreate(
            ['key' => $key],
            ['value' => $storedValue, 'type' => $type]
        );

        Cache::forget("ai_setting_{$key}");
    }

    /**
     * Get all settings as array.
     */
    public static function getAllSettings(): array
    {
        return Cache::remember('ai_settings_all', 3600, function () {
            $settings = [];
            foreach (self::all() as $setting) {
                $settings[$setting->key] = self::castValue($setting->value, $setting->type);
            }
            return $settings;
        });
    }

    /**
     * Clear all settings cache.
     */
    public static function clearCache(): void
    {
        Cache::forget('ai_settings_all');
        foreach (self::pluck('key') as $key) {
            Cache::forget("ai_setting_{$key}");
        }
    }

    /**
     * Cast value to proper type.
     */
    protected static function castValue(mixed $value, ?string $type): mixed
    {
        return match ($type) {
            'boolean' => (bool) $value,
            'integer' => (int) $value,
            'float' => (float) $value,
            'json' => json_decode($value, true),
            default => $value,
        };
    }
}
