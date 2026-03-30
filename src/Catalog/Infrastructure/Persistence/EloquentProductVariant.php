<?php

declare(strict_types=1);

/**
 * This file is part of Meridian Ecommerce Platform.
 *
 * @package    Meridian\Catalog\Infrastructure\Persistence
 * @author     L K Lalitesh <lalitesh@live.com>
 * @company    Bytics Lab
 * @copyright  2026 Bytics Lab. All rights reserved.
 */

namespace Meridian\Catalog\Infrastructure\Persistence;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class EloquentProductVariant extends Model
{
    use SoftDeletes;

    protected $table = 'product_variants';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'id',
        'product_id',
        'sku',
        'name',
        'price_override',
        'cost_override',
        'weight_override',
        'is_active',
        'is_default',
        'sort_order',
        'extra_attributes',
    ];

    protected $casts = [
        'price_override'  => 'integer',
        'cost_override'   => 'integer',
        'weight_override' => 'float',
        'is_active'       => 'boolean',
        'is_default'      => 'boolean',
        'extra_attributes' => 'array',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $variant): void {
            if (empty($variant->id)) {
                $variant->id = (string) Str::ulid();
            }
        });
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(EloquentProduct::class, 'product_id');
    }
}
