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
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Meridian\Catalog\Domain\Product\ProductStatus;
use Meridian\Catalog\Domain\Product\ProductType;
use Meridian\Catalog\Domain\Product\Visibility;

class EloquentProduct extends Model
{
    use SoftDeletes;

    protected $table = 'products';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'id',
        'brand_id',
        'attribute_set_id',
        'tax_class_id',
        'type',
        'status',
        'visibility',
        'name',
        'slug',
        'url_key',
        'sku',
        'main_image',
        'short_description',
        'description',
        'base_price',
        'compare_price',
        'cost_price',
        'weight',
        'weight_unit',
        'is_in_stock',
        'is_purchasable',
        'is_featured',
        'is_new',
        'manage_stock',
        'seo_title',
        'seo_description',
        'seo_robots',
        'canonical_url',
        'og_title',
        'og_description',
        'og_image_url',
        'extra_attributes',
    ];

    protected $casts = [
        'type'           => ProductType::class,
        'status'         => ProductStatus::class,
        'visibility'     => Visibility::class,
        'base_price'     => 'integer',
        'compare_price'  => 'integer',
        'cost_price'     => 'integer',
        'weight'         => 'float',
        'is_in_stock'    => 'boolean',
        'is_purchasable' => 'boolean',
        'is_featured'    => 'boolean',
        'is_new'         => 'boolean',
        'manage_stock'   => 'boolean',
        'extra_attributes' => 'array',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $product): void {
            if (empty($product->id)) {
                $product->id = (string) Str::ulid();
            }
            if (empty($product->slug)) {
                $product->slug = Str::slug($product->name);
            }
            if (empty($product->url_key)) {
                $product->url_key = $product->slug;
            }
        });
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(EloquentBrand::class, 'brand_id');
    }

    public function attributeSet(): BelongsTo
    {
        return $this->belongsTo(EloquentAttributeSet::class, 'attribute_set_id');
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(
            EloquentCategory::class,
            'category_product',
            'product_id',
            'category_id'
        )->withPivot('position', 'is_anchor');
    }

    public function variants(): HasMany
    {
        return $this->hasMany(EloquentProductVariant::class, 'product_id');
    }
}
