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
use Meridian\Catalog\Domain\Repositories\CategoryRepositoryInterface;

final readonly class UpdateCategoryHandler
{
    public function __construct(
        private CategoryRepositoryInterface $categories,
    ) {}

    public function handle(UpdateCategoryCommand $command): void
    {
        $category = $this->categories->findById($command->categoryId);

        if ($category === null) {
            throw new DomainException("Category '{$command->categoryId}' not found.");
        }

        $category->name             = $command->name;
        $category->url_key          = $command->url_key;
        $category->parent_id        = $command->parent_id;
        $category->description      = $command->description;
        $category->is_active       = $command->is_active;
        $category->seo_title       = $command->meta_title;
        $category->seo_description = $command->meta_description;

        $this->categories->save($category);
    }
}
