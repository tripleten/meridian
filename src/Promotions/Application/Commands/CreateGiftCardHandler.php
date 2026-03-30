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

final class CreateGiftCardHandler
{
    public function handle(CreateGiftCardCommand $command): EloquentGiftCard
    {
        $code = $command->code ?? $this->generateCode();

        $card = new EloquentGiftCard();
        $card->code              = $code;
        $card->state             = 'active';
        $card->initial_balance   = $command->initial_balance;
        $card->remaining_balance = $command->initial_balance;
        $card->currency_code     = $command->currency_code;
        $card->customer_id       = $command->customer_id;
        $card->expires_at        = $command->expires_at;
        $card->save();

        return $card;
    }

    private function generateCode(): string
    {
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $code       = '';

        for ($i = 0; $i < 16; $i++) {
            $code .= $characters[random_int(0, strlen($characters) - 1)];
        }

        return $code;
    }
}
