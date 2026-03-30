<?php

declare(strict_types=1);

/**
 * This file is part of Meridian Ecommerce Platform.
 *
 * @package    Meridian\Tax\Infrastructure\Persistence
 * @author     L K Lalitesh <lalitesh@live.com>
 * @company    Bytics Lab
 * @copyright  2026 Bytics Lab. All rights reserved.
 */

namespace Meridian\Tax\Infrastructure\Persistence;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class EloquentTaxRule extends Model
{
    protected $table = 'tax_rules';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'id',
        'name',
        'priority',
        'tax_class_ids',
        'tax_zone_ids',
        'tax_rate_ids',
        'is_active',
    ];

    protected $casts = [
        'tax_class_ids' => 'array',
        'tax_zone_ids'  => 'array',
        'tax_rate_ids'  => 'array',
        'is_active'     => 'boolean',
        'priority'      => 'integer',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $model): void {
            if (empty($model->id)) {
                $model->id = (string) Str::ulid();
            }
        });
    }
}
