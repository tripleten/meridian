<?php

declare(strict_types=1);

/**
 * This file is part of Meridian Ecommerce Platform.
 *
 * @package    Meridian\Catalog\Domain\Events
 * @author     L K Lalitesh <lalitesh@live.com>
 * @company    Bytics Lab
 * @copyright  2026 Bytics Lab. All rights reserved.
 */

namespace Meridian\Catalog\Domain\Events;

use Meridian\Shared\Domain\Events\DomainEvent;

final class ProductPublished extends DomainEvent
{
    public function __construct(
        public readonly string $productId,
        public readonly string $sku,
    ) {
        parent::__construct();
    }

    public function aggregateType(): string
    {
        return 'Product';
    }

    public function aggregateId(): string
    {
        return $this->productId;
    }

    public function toPayload(): array
    {
        return [
            'product_id'  => $this->productId,
            'sku'         => $this->sku,
            'occurred_at' => $this->occurredAt->format('Y-m-d H:i:s'),
        ];
    }
}
