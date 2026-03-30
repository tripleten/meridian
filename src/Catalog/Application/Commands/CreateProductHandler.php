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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Meridian\Catalog\Domain\Events\ProductCreated;
use Meridian\Catalog\Domain\Repositories\ProductRepositoryInterface;
use Meridian\Catalog\Infrastructure\Persistence\EloquentProduct;
use Meridian\Shared\Infrastructure\Outbox\OutboxWriter;

final readonly class CreateProductHandler
{
    public function __construct(
        private ProductRepositoryInterface $products,
        private OutboxWriter               $outbox,
    ) {}

    public function handle(CreateProductCommand $command): void
    {
        if ($this->products->findBySku($command->sku) !== null) {
            throw new DomainException("A product with SKU '{$command->sku}' already exists.");
        }

        DB::transaction(function () use ($command): void {
            $product = EloquentProduct::create([
                'id'                => (string) Str::ulid(),
                'name'              => $command->name,
                'sku'               => $command->sku,
                'type'              => $command->type,
                'status'            => $command->status,
                'base_price'        => $command->base_price,
                'compare_price'     => $command->compare_price,
                'cost_price'        => $command->cost_price,
                'brand_id'          => $command->brand_id,
                'attribute_set_id'  => $command->attribute_set_id,
                'tax_class_id'      => $command->tax_class_id,
                'url_key'           => $command->url_key,
                'short_description' => $command->short_description,
                'description'       => $command->description,
                'weight'            => $command->weight,
                'weight_unit'       => $command->weight_unit,
                'is_featured'       => $command->is_featured,
                'main_image'        => $command->main_image,
            ]);

            if (!empty($command->category_ids)) {
                $product->categories()->sync($command->category_ids);
            }

            $this->outbox->record(new ProductCreated(
                productId: $product->id,
                sku:       $product->sku,
                type:      $product->type->value,
            ));
        });
    }
}
