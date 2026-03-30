<?php

declare(strict_types=1);

namespace Meridian\Settings\Infrastructure\Persistence;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class EloquentSetting extends Model
{
    protected $table = 'settings';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id', 'group', 'key', 'value', 'type', 'is_public', 'updated_by',
    ];

    protected $casts = [
        'is_public' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $model): void {
            if (empty($model->id)) {
                $model->id = (string) Str::ulid();
            }
        });
    }
}
