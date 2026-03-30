<?php

declare(strict_types=1);

/**
 * This file is part of Meridian Ecommerce Platform.
 *
 * @package    Meridian\Promotions\Presentation\Admin
 * @author     L K Lalitesh <lalitesh@live.com>
 * @company    Bytics Lab
 * @copyright  2026 Bytics Lab. All rights reserved.
 */

namespace Meridian\Promotions\Presentation\Admin;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Meridian\Promotions\Application\Commands\CreateGiftCardCommand;
use Meridian\Promotions\Application\Commands\CreateGiftCardHandler;
use Meridian\Promotions\Application\Commands\UpdateGiftCardCommand;
use Meridian\Promotions\Application\Commands\UpdateGiftCardHandler;
use Meridian\Promotions\Application\DTOs\GiftCardData;
use Meridian\Promotions\Domain\GiftCardState;
use Meridian\Promotions\Infrastructure\Persistence\EloquentGiftCard;
use Meridian\Shared\Domain\Exceptions\DomainException;

final class GiftCardController
{
    public function index(): Response
    {
        $giftCards = EloquentGiftCard::orderByDesc('created_at')->paginate(30);

        return Inertia::render('admin/promotions/gift-cards/index', [
            'giftCards' => $giftCards->through(fn ($g) => GiftCardData::fromModel($g)),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('admin/promotions/gift-cards/create');
    }

    public function store(Request $request, CreateGiftCardHandler $handler): RedirectResponse
    {
        $validated = $request->validate([
            'code'          => ['nullable', 'string', 'max:50', 'unique:gift_cards,code'],
            'initial_balance' => ['required', 'integer', 'min:1'],
            'currency_code' => ['required', 'string', 'size:3'],
            'customer_id'   => ['nullable', 'string'],
            'expires_at'    => ['nullable', 'date'],
        ]);

        try {
            $handler->handle(new CreateGiftCardCommand(
                code:            $validated['code'] ?? null,
                initial_balance: (int) $validated['initial_balance'],
                currency_code:   strtoupper($validated['currency_code']),
                customer_id:     $validated['customer_id'] ?? null,
                expires_at:      $validated['expires_at'] ?? null,
            ));
        } catch (DomainException $e) {
            return back()->withErrors(['code' => $e->getMessage()]);
        }

        return redirect('/admin/promotions/gift-cards')->with('success', 'Gift card created.');
    }

    public function show(string $giftCard): Response
    {
        $model = EloquentGiftCard::findOrFail($giftCard);

        return Inertia::render('admin/promotions/gift-cards/show', [
            'giftCard' => GiftCardData::fromModel($model),
        ]);
    }

    public function edit(string $giftCard): Response
    {
        $model = EloquentGiftCard::findOrFail($giftCard);

        return Inertia::render('admin/promotions/gift-cards/edit', [
            'giftCard'     => GiftCardData::fromModel($model),
            'stateOptions' => array_map(
                fn (GiftCardState $s) => ['value' => $s->value, 'label' => $s->label()],
                GiftCardState::cases(),
            ),
        ]);
    }

    public function update(string $giftCard, Request $request, UpdateGiftCardHandler $handler): RedirectResponse
    {
        $validated = $request->validate([
            'state'      => ['required', 'string', 'in:active,redeemed,expired,cancelled'],
            'expires_at' => ['nullable', 'date'],
        ]);

        try {
            $handler->handle(new UpdateGiftCardCommand(
                id:         $giftCard,
                state:      $validated['state'],
                expires_at: $validated['expires_at'] ?? null,
            ));
        } catch (DomainException $e) {
            return back()->withErrors(['state' => $e->getMessage()]);
        }

        return redirect('/admin/promotions/gift-cards')->with('success', 'Gift card updated.');
    }
}
