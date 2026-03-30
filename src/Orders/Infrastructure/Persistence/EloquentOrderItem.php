<?php

declare(strict_types=1);

/**
 * This file is part of Meridian Ecommerce Platform.
 *
 * @package    Meridian\Orders\Infrastructure\Persistence
 * @author     L K Lalitesh <lalitesh@live.com>
 * @company    Bytics Lab
 * @copyright  2026 Bytics Lab. All rights reserved.
 */

namespace Meridian\Orders\Infrastructure\Persistence;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class EloquentOrderItem extends Model
{
    protected $table = 'order_items';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id', 'order_id', 'product_id', 'variant_id', 'sku', 'name',
        'product_snapshot', 'quantity', 'unit_price', 'unit_price_incl_tax',
        'row_total', 'row_total_incl_tax', 'discount_amount', 'tax_amount',
        'tax_rate', 'quantity_refunded', 'refunded_amount',
    ];

    protected $casts = [
        'unit_price'         => 'integer',
        'unit_price_incl_tax'=> 'integer',
        'row_total'          => 'integer',
        'row_total_incl_tax' => 'integer',
        'discount_amount'    => 'integer',
        'tax_amount'         => 'integer',
        'tax_rate'           => 'float',
        'quantity_refunded'  => 'integer',
        'refunded_amount'    => 'integer',
        'product_snapshot'   => 'array',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $model): void {
            if (empty($model->id)) {
                $model->id = (string) Str::ulid();
            }
        });
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(EloquentOrder::class, 'order_id');
    }
}
