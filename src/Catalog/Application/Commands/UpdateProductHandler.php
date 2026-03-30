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
use Meridian\Catalog\Domain\Repositories\ProductRepositoryInterface;

final readonly class UpdateProductHandler
{
    public function __construct(
        private ProductRepositoryInterface $products,
    ) {}

    public function handle(UpdateProductCommand $command): void
    {
        $product = $this->products->findById($command->productId);

        if ($product === null) {
            throw new DomainException("Product '{$command->productId}' not found.");
        }

        $product->name              = $command->name;
        $product->type              = $command->type;
        $product->status            = $command->status;
        $product->base_price        = $command->base_price;
        $product->compare_price     = $command->compare_price;
        $product->cost_price        = $command->cost_price;
        $product->brand_id          = $command->brand_id;
        $product->attribute_set_id  = $command->attribute_set_id;
        $product->tax_class_id      = $command->tax_class_id;
        $product->url_key           = $command->url_key;
        $product->short_description = $command->short_description;
        $product->description       = $command->description;
        $product->weight            = $command->weight;
        $product->weight_unit       = $command->weight_unit;
        $product->is_featured       = $command->is_featured;

        $this->products->save($product);
    }
}
