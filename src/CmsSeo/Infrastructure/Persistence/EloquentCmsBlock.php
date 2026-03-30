<?php

declare(strict_types=1);

/**
 * This file is part of Meridian Ecommerce Platform.
 *
 * @package    Meridian\CmsSeo\Infrastructure\Persistence
 * @author     L K Lalitesh <lalitesh@live.com>
 * @company    Bytics Lab
 * @copyright  2026 Bytics Lab. All rights reserved.
 */

namespace Meridian\CmsSeo\Infrastructure\Persistence;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

final class EloquentCmsBlock extends Model
{
    use SoftDeletes;

    protected $table      = 'cms_blocks';
    protected $keyType    = 'string';
    public    $incrementing = false;

    protected $fillable = [
        'id',
        'channel_id',
        'identifier',
        'title',
        'content',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $block): void {
            if (empty($block->id)) {
                $block->id = (string) Str::ulid();
            }
        });
    }
}
