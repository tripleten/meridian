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
use Meridian\Promotions\Application\Commands\CreateCartRuleCommand;
use Meridian\Promotions\Application\Commands\CreateCartRuleHandler;
use Meridian\Promotions\Application\Commands\UpdateCartRuleCommand;
use Meridian\Promotions\Application\Commands\UpdateCartRuleHandler;
use Meridian\Promotions\Application\DTOs\CartRuleData;
use Meridian\Promotions\Domain\CartDiscountType;
use Meridian\Promotions\Infrastructure\Persistence\EloquentCartRule;

final class CartRuleController
{
    public function index(): Response
    {
        $rules = EloquentCartRule::orderBy('sort_order')->orderByDesc('created_at')->paginate(30);

        return Inertia::render('admin/promotions/cart-rules/index', [
            'rules' => $rules->through(fn ($r) => CartRuleData::fromModel($r)),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('admin/promotions/cart-rules/create', [
            'discountTypeOptions' => array_map(
                fn (CartDiscountType $t) => ['value' => $t->value, 'label' => $t->label()],
                CartDiscountType::cases(),
            ),
        ]);
    }

    public function store(Request $request, CreateCartRuleHandler $handler): RedirectResponse
    {
        $validated = $request->validate([
            'name'                  => ['required', 'string', 'max:150'],
            'description'           => ['nullable', 'string'],
            'discount_type'         => ['required', 'string', 'in:percentage,fixed_cart,fixed_product,buy_x_get_y,free_shipping'],
            'discount_amount'       => ['required', 'numeric', 'min:0'],
            'discount_qty'          => ['nullable', 'integer', 'min:1'],
            'apply_to_shipping'     => ['required', 'boolean'],
            'stop_rules_processing' => ['required', 'boolean'],
            'is_active'             => ['required', 'boolean'],
            'valid_from'            => ['nullable', 'date'],
            'valid_until'           => ['nullable', 'date', 'after_or_equal:valid_from'],
            'uses_per_coupon'       => ['nullable', 'integer', 'min:1'],
            'uses_per_customer'     => ['nullable', 'integer', 'min:1'],
            'sort_order'            => ['required', 'integer'],
        ]);

        $handler->handle(new CreateCartRuleCommand(
            name:                  $validated['name'],
            description:           $validated['description'] ?? null,
            discount_type:         $validated['discount_type'],
            discount_amount:       (float) $validated['discount_amount'],
            discount_qty:          isset($validated['discount_qty']) ? (int) $validated['discount_qty'] : null,
            apply_to_shipping:     (bool) $validated['apply_to_shipping'],
            stop_rules_processing: (bool) $validated['stop_rules_processing'],
            conditions:            null,
            is_active:             (bool) $validated['is_active'],
            valid_from:            $validated['valid_from'] ?? null,
            valid_until:           $validated['valid_until'] ?? null,
            uses_per_coupon:       isset($validated['uses_per_coupon']) ? (int) $validated['uses_per_coupon'] : null,
            uses_per_customer:     isset($validated['uses_per_customer']) ? (int) $validated['uses_per_customer'] : null,
            sort_order:            (int) $validated['sort_order'],
        ));

        return redirect('/admin/promotions/cart-rules')->with('success', 'Cart rule created.');
    }

    public function edit(string $rule): Response
    {
        $model = EloquentCartRule::findOrFail($rule);

        return Inertia::render('admin/promotions/cart-rules/edit', [
            'rule'                => CartRuleData::fromModel($model),
            'discountTypeOptions' => array_map(
                fn (CartDiscountType $t) => ['value' => $t->value, 'label' => $t->label()],
                CartDiscountType::cases(),
            ),
        ]);
    }

    public function update(string $rule, Request $request, UpdateCartRuleHandler $handler): RedirectResponse
    {
        $validated = $request->validate([
            'name'                  => ['required', 'string', 'max:150'],
            'description'           => ['nullable', 'string'],
            'discount_type'         => ['required', 'string', 'in:percentage,fixed_cart,fixed_product,buy_x_get_y,free_shipping'],
            'discount_amount'       => ['required', 'numeric', 'min:0'],
            'discount_qty'          => ['nullable', 'integer', 'min:1'],
            'apply_to_shipping'     => ['required', 'boolean'],
            'stop_rules_processing' => ['required', 'boolean'],
            'is_active'             => ['required', 'boolean'],
            'valid_from'            => ['nullable', 'date'],
            'valid_until'           => ['nullable', 'date', 'after_or_equal:valid_from'],
            'uses_per_coupon'       => ['nullable', 'integer', 'min:1'],
            'uses_per_customer'     => ['nullable', 'integer', 'min:1'],
            'sort_order'            => ['required', 'integer'],
        ]);

        $handler->handle(new UpdateCartRuleCommand(
            id:                    $rule,
            name:                  $validated['name'],
            description:           $validated['description'] ?? null,
            discount_type:         $validated['discount_type'],
            discount_amount:       (float) $validated['discount_amount'],
            discount_qty:          isset($validated['discount_qty']) ? (int) $validated['discount_qty'] : null,
            apply_to_shipping:     (bool) $validated['apply_to_shipping'],
            stop_rules_processing: (bool) $validated['stop_rules_processing'],
            conditions:            null,
            is_active:             (bool) $validated['is_active'],
            valid_from:            $validated['valid_from'] ?? null,
            valid_until:           $validated['valid_until'] ?? null,
            uses_per_coupon:       isset($validated['uses_per_coupon']) ? (int) $validated['uses_per_coupon'] : null,
            uses_per_customer:     isset($validated['uses_per_customer']) ? (int) $validated['uses_per_customer'] : null,
            sort_order:            (int) $validated['sort_order'],
        ));

        return redirect('/admin/promotions/cart-rules')->with('success', 'Cart rule updated.');
    }
}
