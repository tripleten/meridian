<?php

declare(strict_types=1);

/**
 * This file is part of Meridian Ecommerce Platform.
 *
 * @package    Meridian\CmsSeo
 * @author     L K Lalitesh <lalitesh@live.com>
 * @company    Bytics Lab
 * @copyright  2026 Bytics Lab. All rights reserved.
 */

namespace Meridian\CmsSeo;

use Illuminate\Support\ServiceProvider;
use Meridian\CmsSeo\Domain\Repositories\CmsBlockRepositoryInterface;
use Meridian\CmsSeo\Domain\Repositories\CmsPageRepositoryInterface;
use Meridian\CmsSeo\Infrastructure\Persistence\EloquentCmsBlockRepository;
use Meridian\CmsSeo\Infrastructure\Persistence\EloquentCmsPageRepository;

final class CmsSeoServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(CmsPageRepositoryInterface::class, EloquentCmsPageRepository::class);
        $this->app->bind(CmsBlockRepositoryInterface::class, EloquentCmsBlockRepository::class);
    }

    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__ . '/routes.php');
    }
}
