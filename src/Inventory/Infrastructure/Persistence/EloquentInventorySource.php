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
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use Meridian\Inventory\Domain\Source\SourceType;

class EloquentInventorySource extends Model
{
    protected $table = 'inventory_sources';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'id',
        'name',
        'code',
        'type',
        'address_line1',
        'city',
        'country_code',
        'is_active',
        'is_default',
        'priority',
    ];

    protected $casts = [
        'type'      => SourceType::class,
        'is_active' => 'boolean',
        'is_default'=> 'boolean',
        'priority'  => 'integer',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $model): void {
            if (empty($model->id)) {
                $model->id = (string) Str::ulid();
            }
        });
    }

    public function inventoryItems(): HasMany
    {
        return $this->hasMany(EloquentInventoryItem::class, 'source_id');
    }
}
