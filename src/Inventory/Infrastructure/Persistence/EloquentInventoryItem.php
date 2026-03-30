<?php

declare(strict_types=1);

/**
 * This file is part of Meridian Ecommerce Platform.
 *
 * @package    Meridian\Inventory\Infrastructure\Persistence
 * @author     L K Lalitesh <lalitesh@live.com>
 * @company    Bytics Lab
 * @copyright  2026 Bytics Lab. All rights reserved.
 */

namespace Meridian\Inventory\Infrastructure\Persistence;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;
use Meridian\Catalog\Infrastructure\Persistence\EloquentProductVariant;

class EloquentInventoryItem extends Model
{
    protected $table = 'inventory_items';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'id',
        'product_variant_id',
        'source_id',
        'qty_available',
        'qty_reserved',
        'qty_incoming',
        'low_stock_threshold',
        'backorders_allowed',
        'manage_stock',
    ];

    protected $casts = [
        'qty_available'       => 'integer',
        'qty_reserved'        => 'integer',
        'qty_incoming'        => 'integer',
        'low_stock_threshold' => 'integer',
        'backorders_allowed'  => 'boolean',
        'manage_stock'        => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $model): void {
            if (empty($model->id)) {
                $model->id = (string) Str::ulid();
            }
        });
    }

    public function productVariant(): BelongsTo
    {
        return $this->belongsTo(EloquentProductVariant::class, 'product_variant_id');
    }

    public function inventorySource(): BelongsTo
    {
        return $this->belongsTo(EloquentInventorySource::class, 'source_id');
    }
}
