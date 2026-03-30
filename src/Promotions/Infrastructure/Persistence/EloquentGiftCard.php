<?php

declare(strict_types=1);

/**
 * This file is part of Meridian Ecommerce Platform.
 *
 * @package    Meridian\Promotions\Infrastructure\Persistence
 * @author     L K Lalitesh <lalitesh@live.com>
 * @company    Bytics Lab
 * @copyright  2026 Bytics Lab. All rights reserved.
 */

namespace Meridian\Promotions\Infrastructure\Persistence;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Meridian\Promotions\Domain\GiftCardState;

class EloquentGiftCard extends Model
{
    use SoftDeletes;

    protected $table = 'gift_cards';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id', 'code', 'state',
        'initial_balance', 'remaining_balance',
        'currency_code', 'customer_id', 'order_id', 'expires_at',
    ];

    protected $casts = [
        'state'             => GiftCardState::class,
        'initial_balance'   => 'integer',
        'remaining_balance' => 'integer',
        'expires_at'        => 'datetime',
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
