<?php

declare(strict_types=1);

/**
 * This file is part of Meridian Ecommerce Platform.
 *
 * @package    Meridian\Promotions\Application\Commands
 * @author     L K Lalitesh <lalitesh@live.com>
 * @company    Bytics Lab
 * @copyright  2026 Bytics Lab. All rights reserved.
 */

namespace Meridian\Promotions\Application\Commands;

use Meridian\Promotions\Infrastructure\Persistence\EloquentGiftCard;
use Meridian\Shared\Domain\Exceptions\DomainException;

final class UpdateGiftCardHandler
{
    public function handle(UpdateGiftCardCommand $command): EloquentGiftCard
    {
        $card = EloquentGiftCard::find($command->id);

        if ($card === null) {
            throw new DomainException("Gift card '{$command->id}' not found.");
        }

        $card->state      = $command->state;
        $card->expires_at = $command->expires_at;
        $card->save();

        return $card;
    }
}
