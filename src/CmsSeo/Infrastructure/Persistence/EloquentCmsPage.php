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

final class EloquentCmsPage extends Model
{
    use SoftDeletes;

    protected $table      = 'cms_pages';
    protected $keyType    = 'string';
    public    $incrementing = false;

    protected $fillable = [
        'id',
        'channel_id',
        'title',
        'url_key',
        'content',
        'state',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'meta_robots_noindex',
        'layout_config',
        'published_at',
    ];

    protected $casts = [
        'meta_robots_noindex' => 'boolean',
        'layout_config'       => 'array',
        'published_at'        => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $page): void {
            if (empty($page->id)) {
                $page->id = (string) Str::ulid();
            }
        });
    }
}
