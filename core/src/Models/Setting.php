<?php

namespace Core\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class Setting extends Model
{
    protected $connection = 'core';

    protected $fillable = ['group', 'key', 'value', 'user_id'];

    public static function getUserValue(string $key, mixed $default = null, ?int $userId = null): mixed
    {
        try {
            if (! Schema::hasTable((new static)->getTable())) {
                return $default;
            }

            $userId ??= auth()->id();
            if ($userId) {
                $value = static::query()->where('key', $key)->where('user_id', $userId)->value('value');
                if ($value !== null) {
                    return $value;
                }
            }

            return static::query()->where('key', $key)->whereNull('user_id')->value('value') ?? $default;
        } catch (\Throwable) {
            return $default;
        }
    }

    public static function setUserValue(string $key, mixed $value, ?string $group = null, ?int $userId = null): void
    {
        static::query()->updateOrCreate(
            ['key' => $key, 'user_id' => $userId ?? auth()->id()],
            ['group' => $group, 'value' => is_scalar($value) ? (string) $value : json_encode($value)],
        );
    }

    public static function setValue(string $key, mixed $value, ?string $group = null): void
    {
        static::query()->updateOrCreate(
            ['key' => $key, 'user_id' => null],
            ['group' => $group, 'value' => is_scalar($value) ? (string) $value : json_encode($value)],
        );
    }
}
