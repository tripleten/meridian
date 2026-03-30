<?php

declare(strict_types=1);

namespace Meridian\Payments\Infrastructure\Persistence;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class EloquentPaymentMethod extends Model
{
    protected $table = 'payment_methods';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id', 'code', 'name', 'description',
        'is_active', 'sort_order', 'config',
    ];

    protected $casts = [
        'is_active'  => 'boolean',
        'sort_order' => 'integer',
        'config'     => 'array',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $model): void {
            if (empty($model->id)) {
                $model->id = (string) Str::ulid();
            }
        });
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(EloquentTransaction::class, 'payment_method_id');
    }
}
