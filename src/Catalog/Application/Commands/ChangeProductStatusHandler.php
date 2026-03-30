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
use Meridian\Catalog\Domain\Events\ProductPublished;
use Meridian\Catalog\Domain\Product\ProductStatus;
use Meridian\Catalog\Domain\Repositories\ProductRepositoryInterface;
use Meridian\Shared\Domain\Exceptions\InvalidStateTransition;
use Meridian\Shared\Infrastructure\Outbox\OutboxWriter;

final readonly class ChangeProductStatusHandler
{
    public function __construct(
        private ProductRepositoryInterface $products,
        private OutboxWriter               $outbox,
    ) {}

    public function handle(ChangeProductStatusCommand $command): void
    {
        $product = $this->products->findById($command->productId);

        if ($product === null) {
            throw new DomainException("Product '{$command->productId}' not found.");
        }

        $current = $product->status instanceof ProductStatus
            ? $product->status
            : ProductStatus::from($product->status);
        $target  = ProductStatus::from($command->newStatus);

        if (!$current->canTransitionTo($target)) {
            throw InvalidStateTransition::for('Product', $current->value, $target->value);
        }

        DB::transaction(function () use ($product, $target, $command): void {
            $product->status = $target->value;
            $this->products->save($product);

            if ($target === ProductStatus::Active) {
                $this->outbox->record(new ProductPublished(
                    productId: (string) $product->id,
                    sku:       $product->sku,
                ));
            }
        });
    }
}
