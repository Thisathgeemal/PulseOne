<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserPreference extends Model
{
    protected $fillable = [
        'user_id',
        'key',
        'value',
    ];

    protected $casts = [
        'value' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get a preference value for a user
     */
    public static function get(int $userId, string $key, mixed $default = null): mixed
    {
        $preference = static::where('user_id', $userId)
            ->where('key', $key)
            ->first();

        return $preference ? $preference->value : $default;
    }

    /**
     * Set a preference value for a user
     */
    public static function set(int $userId, string $key, mixed $value): void
    {
        static::updateOrCreate(
            ['user_id' => $userId, 'key' => $key],
            ['value' => $value]
        );
    }

    /**
     * Get default appearance preferences
     */
    public static function getDefaultAppearance(): array
    {
        return [
            'theme_mode' => 'auto',
            'accent_color' => '#ef4444',
            'sidebar_position' => 'left',
            'sidebar_compact' => false,
            'density' => 'comfortable',
            'font_size' => 'normal',
        ];
    }

    /**
     * Get user's appearance preferences with defaults
     */
    public static function getAppearance(int $userId): array
    {
        $saved = static::get($userId, 'appearance', []);
        $defaults = static::getDefaultAppearance();
        
        return array_merge($defaults, $saved);
    }
}
