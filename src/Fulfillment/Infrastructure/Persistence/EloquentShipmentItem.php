<?php

declare(strict_types=1);

namespace Meridian\Fulfillment\Infrastructure\Persistence;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class EloquentShipmentItem extends Model
{
    protected $table = 'shipment_items';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id', 'shipment_id', 'order_item_id',
        'sku', 'name', 'quantity_shipped',
    ];

    protected $casts = [
        'quantity_shipped' => 'integer',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $model): void {
            if (empty($model->id)) {
                $model->id = (string) Str::ulid();
            }
        });
    }

    public function shipment(): BelongsTo
    {
        return $this->belongsTo(EloquentShipment::class, 'shipment_id');
    }
}
