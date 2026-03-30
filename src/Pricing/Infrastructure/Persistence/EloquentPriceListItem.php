<?php

declare(strict_types=1);

/**
 * This file is part of Meridian Ecommerce Platform.
 *
 * @package    Meridian\Pricing\Infrastructure\Persistence
 * @author     L K Lalitesh <lalitesh@live.com>
 * @company    Bytics Lab
 * @copyright  2026 Bytics Lab. All rights reserved.
 */

namespace Meridian\Pricing\Infrastructure\Persistence;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;
use Meridian\Catalog\Infrastructure\Persistence\EloquentProduct;

class EloquentPriceListItem extends Model
{
    protected $table = 'price_list_items';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id', 'price_list_id', 'product_id', 'product_variant_id',
        'price', 'compare_price', 'valid_from', 'valid_until',
    ];

    protected $casts = [
        'price'         => 'integer',
        'compare_price' => 'integer',
        'valid_from'    => 'date',
        'valid_until'   => 'date',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $model): void {
            if (empty($model->id)) {
                $model->id = (string) Str::ulid();
            }
        });
    }

    public function priceList(): BelongsTo
    {
        return $this->belongsTo(EloquentPriceList::class, 'price_list_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(EloquentProduct::class, 'product_id');
    }
}
