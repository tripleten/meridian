<?php

declare(strict_types=1);

namespace Meridian\Webhooks\Infrastructure\Persistence;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class EloquentWebhookDelivery extends Model
{
    protected $table = 'webhook_deliveries';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id', 'endpoint_id', 'event_type', 'payload',
        'response_status', 'response_body', 'attempt_count',
        'next_retry_at', 'state',
    ];

    protected $casts = [
        'payload'       => 'array',
        'attempt_count' => 'integer',
        'next_retry_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $model): void {
            if (empty($model->id)) {
                $model->id = (string) Str::ulid();
            }
        });
    }

    public function endpoint(): BelongsTo
    {
        return $this->belongsTo(EloquentWebhookEndpoint::class, 'endpoint_id');
    }
}
