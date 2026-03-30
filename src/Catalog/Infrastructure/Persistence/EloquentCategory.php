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
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class EloquentCategory extends Model
{
    protected $table = 'categories';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'id',
        'parent_id',
        'name',
        'slug',
        'url_key',
        'description',
        'short_description',
        'is_active',
        'sort_mode',
        'position',
        '_lft',
        '_rgt',
        'depth',
        'seo_title',
        'seo_description',
        'seo_robots',
        'canonical_url',
        'og_title',
        'og_description',
        'og_image_url',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        '_lft'      => 'integer',
        '_rgt'      => 'integer',
        'depth'     => 'integer',
        'position'  => 'integer',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $category): void {
            if (empty($category->id)) {
                $category->id = (string) Str::ulid();
            }
            if (empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }
            if (empty($category->url_key)) {
                $category->url_key = $category->slug;
            }
        });
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('position');
    }
}
