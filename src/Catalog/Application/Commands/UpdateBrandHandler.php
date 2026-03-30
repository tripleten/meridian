<?php

declare(strict_types=1);

/**
 * This file is part of Meridian Ecommerce Platform.
 *
 * @package    Meridian\Catalog\Application\Commands
 * @author     L K Lalitesh <lalitesh@live.com>
 * @company    Bytics Lab
 * @copyright  2026 Bytics Lab. All rights reserved.
 */

namespace Meridian\Catalog\Application\Commands;

use DomainException;
use Meridian\Catalog\Domain\Repositories\BrandRepositoryInterface;

final readonly class UpdateBrandHandler
{
    public function __construct(
        private BrandRepositoryInterface $brands,
    ) {}

    public function handle(UpdateBrandCommand $command): void
    {
        $brand = $this->brands->findById($command->brandId);

        if ($brand === null) {
            throw new DomainException("Brand '{$command->brandId}' not found.");
        }

        $brand->name        = $command->name;
        $brand->slug        = $command->slug;
        $brand->description = $command->description;
        $brand->is_active   = $command->is_active;

        $this->brands->save($brand);
    }
}
