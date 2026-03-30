<?php

declare(strict_types=1);

namespace Meridian\Webhooks\Infrastructure\Persistence;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class EloquentWebhookEndpoint extends Model
{
    use SoftDeletes;

    protected $table = 'webhook_endpoints';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id', 'url', 'secret', 'is_active', 'subscribed_events',
    ];

    protected $casts = [
        'is_active'         => 'boolean',
        'subscribed_events' => 'array',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $model): void {
            if (empty($model->id)) {
                $model->id = (string) Str::ulid();
            }
        });
    }

    public function deliveries(): HasMany
    {
        return $this->hasMany(EloquentWebhookDelivery::class, 'endpoint_id');
    }
}
