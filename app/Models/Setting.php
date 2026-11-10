<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'key',
        'value',
    ];

    /**
     * Get a setting value by key.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function get(string $key, $default = null)
    {
        return Cache::rememberForever("setting.{$key}", function () use ($key, $default) {
            $setting = static::where('key', $key)->first();
            return $setting ? $setting->value : $default;
        });
    }

    /**
     * Set/update a setting value.
     *
     * @param string $key
     * @param mixed $value
     * @return static
     */
    public static function set(string $key, $value)
    {
        $setting = static::updateOrCreate(
            ['key' => $key],
            ['value' => is_array($value) ? json_encode($value) : $value]
        );

        Cache::forget("setting.{$key}");
        Cache::forget("settings.all");

        return $setting;
    }

    /**
     * Check if a setting key exists.
     *
     * @param string $key
     * @return bool
     */
    public static function has(string $key): bool
    {
        return static::where('key', $key)->exists();
    }

    /**
     * Get all settings as key => value array.
     *
     * @return array
     */
    public static function getAll(): array
    {
        return Cache::rememberForever('settings.all', function () {
            return static::pluck('value', 'key')->toArray();
        });
    }
}
