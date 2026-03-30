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
use Meridian\Promotions\Application\Commands\CreateCatalogPriceRuleCommand;
use Meridian\Promotions\Application\Commands\CreateCatalogPriceRuleHandler;
use Meridian\Promotions\Application\Commands\UpdateCatalogPriceRuleCommand;
use Meridian\Promotions\Application\Commands\UpdateCatalogPriceRuleHandler;
use Meridian\Promotions\Application\DTOs\CatalogPriceRuleData;
use Meridian\Promotions\Domain\CatalogDiscountType;
use Meridian\Promotions\Infrastructure\Persistence\EloquentCatalogPriceRule;

final class CatalogPriceRuleController
{
    public function index(): Response
    {
        $rules = EloquentCatalogPriceRule::orderBy('priority')->orderByDesc('created_at')->get();

        return Inertia::render('admin/promotions/catalog-rules/index', [
            'rules' => $rules->map(fn ($r) => CatalogPriceRuleData::fromModel($r))->values()->all(),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('admin/promotions/catalog-rules/create', [
            'discountTypeOptions' => array_map(
                fn (CatalogDiscountType $t) => ['value' => $t->value, 'label' => $t->label()],
                CatalogDiscountType::cases(),
            ),
        ]);
    }

    public function store(Request $request, CreateCatalogPriceRuleHandler $handler): RedirectResponse
    {
        $validated = $request->validate([
            'name'               => ['required', 'string', 'max:150'],
            'description'        => ['nullable', 'string'],
            'discount_type'      => ['required', 'string', 'in:percentage,fixed,to_fixed'],
            'discount_amount'    => ['required', 'numeric', 'min:0'],
            'is_active'          => ['required', 'boolean'],
            'priority'           => ['required', 'integer'],
            'stop_further_rules' => ['required', 'boolean'],
            'valid_from'         => ['nullable', 'date'],
            'valid_until'        => ['nullable', 'date', 'after_or_equal:valid_from'],
        ]);

        $handler->handle(new CreateCatalogPriceRuleCommand(
            name:               $validated['name'],
            description:        $validated['description'] ?? null,
            discount_type:      $validated['discount_type'],
            discount_amount:    (float) $validated['discount_amount'],
            is_active:          (bool) $validated['is_active'],
            priority:           (int) $validated['priority'],
            stop_further_rules: (bool) $validated['stop_further_rules'],
            category_ids:       null,
            valid_from:         $validated['valid_from'] ?? null,
            valid_until:        $validated['valid_until'] ?? null,
        ));

        return redirect('/admin/promotions/catalog-rules')->with('success', 'Catalog price rule created.');
    }

    public function edit(string $rule): Response
    {
        $model = EloquentCatalogPriceRule::findOrFail($rule);

        return Inertia::render('admin/promotions/catalog-rules/edit', [
            'rule'                => CatalogPriceRuleData::fromModel($model),
            'discountTypeOptions' => array_map(
                fn (CatalogDiscountType $t) => ['value' => $t->value, 'label' => $t->label()],
                CatalogDiscountType::cases(),
            ),
        ]);
    }

    public function update(string $rule, Request $request, UpdateCatalogPriceRuleHandler $handler): RedirectResponse
    {
        $validated = $request->validate([
            'name'               => ['required', 'string', 'max:150'],
            'description'        => ['nullable', 'string'],
            'discount_type'      => ['required', 'string', 'in:percentage,fixed,to_fixed'],
            'discount_amount'    => ['required', 'numeric', 'min:0'],
            'is_active'          => ['required', 'boolean'],
            'priority'           => ['required', 'integer'],
            'stop_further_rules' => ['required', 'boolean'],
            'valid_from'         => ['nullable', 'date'],
            'valid_until'        => ['nullable', 'date', 'after_or_equal:valid_from'],
        ]);

        $handler->handle(new UpdateCatalogPriceRuleCommand(
            id:                 $rule,
            name:               $validated['name'],
            description:        $validated['description'] ?? null,
            discount_type:      $validated['discount_type'],
            discount_amount:    (float) $validated['discount_amount'],
            is_active:          (bool) $validated['is_active'],
            priority:           (int) $validated['priority'],
            stop_further_rules: (bool) $validated['stop_further_rules'],
            category_ids:       null,
            valid_from:         $validated['valid_from'] ?? null,
            valid_until:        $validated['valid_until'] ?? null,
        ));

        return redirect('/admin/promotions/catalog-rules')->with('success', 'Catalog price rule updated.');
    }
}
