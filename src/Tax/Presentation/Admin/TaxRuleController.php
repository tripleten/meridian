<?php

declare(strict_types=1);

/**
 * This file is part of Meridian Ecommerce Platform.
 *
 * @package    Meridian\Tax\Presentation\Admin
 * @author     L K Lalitesh <lalitesh@live.com>
 * @company    Bytics Lab
 * @copyright  2026 Bytics Lab. All rights reserved.
 */

namespace Meridian\Tax\Presentation\Admin;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Meridian\Shared\Domain\Exceptions\DomainException;
use Meridian\Tax\Application\Commands\CreateTaxRuleCommand;
use Meridian\Tax\Application\Commands\CreateTaxRuleHandler;
use Meridian\Tax\Application\Commands\UpdateTaxRuleCommand;
use Meridian\Tax\Application\Commands\UpdateTaxRuleHandler;
use Meridian\Tax\Application\DTOs\TaxRuleData;
use Meridian\Tax\Infrastructure\Persistence\EloquentTaxClass;
use Meridian\Tax\Infrastructure\Persistence\EloquentTaxRate;
use Meridian\Tax\Infrastructure\Persistence\EloquentTaxRule;
use Meridian\Tax\Infrastructure\Persistence\EloquentTaxZone;

final class TaxRuleController
{
    public function index(): Response
    {
        $rules = EloquentTaxRule::orderBy('priority')
            ->get()
            ->map(fn (EloquentTaxRule $m) => TaxRuleData::fromModel($m));

        return Inertia::render('admin/tax/rules/index', [
            'rules' => $rules,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('admin/tax/rules/create', [
            'classes' => EloquentTaxClass::orderBy('name')->get(['id', 'name']),
            'zones'   => EloquentTaxZone::orderBy('name')->get(['id', 'name']),
            'rates'   => EloquentTaxRate::orderBy('name')->get(['id', 'name', 'code']),
        ]);
    }

    public function store(Request $request, CreateTaxRuleHandler $handler): RedirectResponse
    {
        $validated = $request->validate([
            'name'          => ['required', 'string', 'max:100'],
            'priority'      => ['integer'],
            'tax_class_ids' => ['array'],
            'tax_zone_ids'  => ['array'],
            'tax_rate_ids'  => ['array'],
            'is_active'     => ['boolean'],
        ]);

        try {
            $handler->handle(new CreateTaxRuleCommand(
                name:          $validated['name'],
                priority:      (int) ($validated['priority'] ?? 0),
                tax_class_ids: $validated['tax_class_ids'] ?? [],
                tax_zone_ids:  $validated['tax_zone_ids'] ?? [],
                tax_rate_ids:  $validated['tax_rate_ids'] ?? [],
                is_active:     (bool) ($validated['is_active'] ?? true),
            ));
        } catch (DomainException $e) {
            return back()->withErrors(['name' => $e->getMessage()]);
        }

        return redirect()->route('admin.tax.rules.index')->with('success', 'Tax rule created.');
    }

    public function edit(string $rule): Response
    {
        $model = EloquentTaxRule::findOrFail($rule);

        return Inertia::render('admin/tax/rules/edit', [
            'rule'    => TaxRuleData::fromModel($model),
            'classes' => EloquentTaxClass::orderBy('name')->get(['id', 'name']),
            'zones'   => EloquentTaxZone::orderBy('name')->get(['id', 'name']),
            'rates'   => EloquentTaxRate::orderBy('name')->get(['id', 'name', 'code']),
        ]);
    }

    public function update(string $rule, Request $request, UpdateTaxRuleHandler $handler): RedirectResponse
    {
        $validated = $request->validate([
            'name'          => ['required', 'string', 'max:100'],
            'priority'      => ['integer'],
            'tax_class_ids' => ['array'],
            'tax_zone_ids'  => ['array'],
            'tax_rate_ids'  => ['array'],
            'is_active'     => ['boolean'],
        ]);

        try {
            $handler->handle(new UpdateTaxRuleCommand(
                id:            $rule,
                name:          $validated['name'],
                priority:      (int) ($validated['priority'] ?? 0),
                tax_class_ids: $validated['tax_class_ids'] ?? [],
                tax_zone_ids:  $validated['tax_zone_ids'] ?? [],
                tax_rate_ids:  $validated['tax_rate_ids'] ?? [],
                is_active:     (bool) ($validated['is_active'] ?? true),
            ));
        } catch (DomainException $e) {
            return back()->withErrors(['name' => $e->getMessage()]);
        }

        return redirect()->route('admin.tax.rules.index')->with('success', 'Tax rule updated.');
    }
}
