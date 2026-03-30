<?php

declare(strict_types=1);

namespace Meridian\Fulfillment\Infrastructure\Persistence;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use Meridian\Fulfillment\Domain\ShipmentState;

class EloquentShipment extends Model
{
    protected $table = 'shipments';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id', 'order_id', 'state', 'carrier',
        'tracking_number', 'tracking_url',
        'shipped_at', 'delivered_at', 'notes',
    ];

    protected $casts = [
        'state'        => ShipmentState::class,
        'shipped_at'   => 'datetime',
        'delivered_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $model): void {
            if (empty($model->id)) {
                $model->id = (string) Str::ulid();
            }
        });
    }

    public function items(): HasMany
    {
        return $this->hasMany(EloquentShipmentItem::class, 'shipment_id');
    }
}
