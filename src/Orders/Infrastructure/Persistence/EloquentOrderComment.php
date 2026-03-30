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

class EloquentOrderComment extends Model
{
    protected $table = 'order_comments';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id', 'order_id', 'author_type', 'author_id',
        'comment', 'is_customer_notified', 'is_visible_to_customer',
    ];

    protected $casts = [
        'is_customer_notified'   => 'boolean',
        'is_visible_to_customer' => 'boolean',
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
