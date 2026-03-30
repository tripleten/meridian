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
use Meridian\Promotions\Application\Commands\CreateCouponCommand;
use Meridian\Promotions\Application\Commands\CreateCouponHandler;
use Meridian\Promotions\Application\Commands\UpdateCouponCommand;
use Meridian\Promotions\Application\Commands\UpdateCouponHandler;
use Meridian\Promotions\Application\DTOs\CouponData;
use Meridian\Promotions\Domain\CouponType;
use Meridian\Promotions\Infrastructure\Persistence\EloquentCoupon;
use Meridian\Shared\Domain\Exceptions\DomainException;

final class CouponController
{
    public function index(): Response
    {
        $coupons = EloquentCoupon::orderByDesc('created_at')->paginate(30);

        return Inertia::render('admin/promotions/coupons/index', [
            'coupons' => $coupons->through(fn ($c) => CouponData::fromModel($c)),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('admin/promotions/coupons/create', [
            'typeOptions' => array_map(
                fn (CouponType $t) => ['value' => $t->value, 'label' => $t->label()],
                CouponType::cases(),
            ),
        ]);
    }

    public function store(Request $request, CreateCouponHandler $handler): RedirectResponse
    {
        $validated = $request->validate([
            'code'                       => ['required', 'string', 'max:50', 'unique:coupons,code'],
            'description'                => ['nullable', 'string', 'max:200'],
            'type'                       => ['required', 'string', 'in:single_use,multi_use,per_customer'],
            'usage_limit'                => ['nullable', 'integer', 'min:1'],
            'usage_limit_per_customer'   => ['nullable', 'integer', 'min:1'],
            'cart_rule_id'               => ['nullable', 'string'],
            'is_active'                  => ['required', 'boolean'],
            'valid_from'                 => ['nullable', 'date'],
            'valid_until'                => ['nullable', 'date', 'after_or_equal:valid_from'],
        ]);

        try {
            $handler->handle(new CreateCouponCommand(
                code:                     strtoupper($validated['code']),
                description:              $validated['description'] ?? null,
                type:                     $validated['type'],
                usage_limit:              isset($validated['usage_limit']) ? (int) $validated['usage_limit'] : null,
                usage_limit_per_customer: isset($validated['usage_limit_per_customer']) ? (int) $validated['usage_limit_per_customer'] : null,
                cart_rule_id:             $validated['cart_rule_id'] ?? null,
                is_active:                (bool) $validated['is_active'],
                valid_from:               $validated['valid_from'] ?? null,
                valid_until:              $validated['valid_until'] ?? null,
            ));
        } catch (DomainException $e) {
            return back()->withErrors(['code' => $e->getMessage()]);
        }

        return redirect('/admin/promotions/coupons')->with('success', 'Coupon created.');
    }

    public function edit(string $coupon): Response
    {
        $model = EloquentCoupon::findOrFail($coupon);

        return Inertia::render('admin/promotions/coupons/edit', [
            'coupon'      => CouponData::fromModel($model),
            'typeOptions' => array_map(
                fn (CouponType $t) => ['value' => $t->value, 'label' => $t->label()],
                CouponType::cases(),
            ),
        ]);
    }

    public function update(string $coupon, Request $request, UpdateCouponHandler $handler): RedirectResponse
    {
        $validated = $request->validate([
            'code'                       => ['required', 'string', 'max:50', "unique:coupons,code,{$coupon}"],
            'description'                => ['nullable', 'string', 'max:200'],
            'type'                       => ['required', 'string', 'in:single_use,multi_use,per_customer'],
            'usage_limit'                => ['nullable', 'integer', 'min:1'],
            'usage_limit_per_customer'   => ['nullable', 'integer', 'min:1'],
            'cart_rule_id'               => ['nullable', 'string'],
            'is_active'                  => ['required', 'boolean'],
            'valid_from'                 => ['nullable', 'date'],
            'valid_until'                => ['nullable', 'date', 'after_or_equal:valid_from'],
        ]);

        $handler->handle(new UpdateCouponCommand(
            id:                       $coupon,
            code:                     strtoupper($validated['code']),
            description:              $validated['description'] ?? null,
            type:                     $validated['type'],
            usage_limit:              isset($validated['usage_limit']) ? (int) $validated['usage_limit'] : null,
            usage_limit_per_customer: isset($validated['usage_limit_per_customer']) ? (int) $validated['usage_limit_per_customer'] : null,
            cart_rule_id:             $validated['cart_rule_id'] ?? null,
            is_active:                (bool) $validated['is_active'],
            valid_from:               $validated['valid_from'] ?? null,
            valid_until:              $validated['valid_until'] ?? null,
        ));

        return redirect('/admin/promotions/coupons')->with('success', 'Coupon updated.');
    }
}
