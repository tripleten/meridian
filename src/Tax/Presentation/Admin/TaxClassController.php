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
use Meridian\Tax\Application\Commands\CreateTaxClassCommand;
use Meridian\Tax\Application\Commands\CreateTaxClassHandler;
use Meridian\Tax\Application\Commands\UpdateTaxClassCommand;
use Meridian\Tax\Application\Commands\UpdateTaxClassHandler;
use Meridian\Tax\Application\DTOs\TaxClassData;
use Meridian\Tax\Infrastructure\Persistence\EloquentTaxClass;

final class TaxClassController
{
    public function index(): Response
    {
        $classes = EloquentTaxClass::orderBy('name')
            ->get()
            ->map(fn (EloquentTaxClass $m) => TaxClassData::fromModel($m));

        return Inertia::render('admin/tax/classes/index', [
            'classes' => $classes,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('admin/tax/classes/create');
    }

    public function store(Request $request, CreateTaxClassHandler $handler): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'code' => ['required', 'string', 'max:50', 'unique:tax_classes,code'],
        ]);

        try {
            $handler->handle(new CreateTaxClassCommand(
                name: $validated['name'],
                code: $validated['code'],
            ));
        } catch (DomainException $e) {
            return back()->withErrors(['code' => $e->getMessage()]);
        }

        return redirect()->route('admin.tax.classes.index')->with('success', 'Tax class created.');
    }

    public function edit(string $taxClass): Response
    {
        $model = EloquentTaxClass::findOrFail($taxClass);

        return Inertia::render('admin/tax/classes/edit', [
            'taxClass' => TaxClassData::fromModel($model),
        ]);
    }

    public function update(string $taxClass, Request $request, UpdateTaxClassHandler $handler): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'code' => ['required', 'string', 'max:50', "unique:tax_classes,code,{$taxClass}"],
        ]);

        try {
            $handler->handle(new UpdateTaxClassCommand(
                id:   $taxClass,
                name: $validated['name'],
                code: $validated['code'],
            ));
        } catch (DomainException $e) {
            return back()->withErrors(['code' => $e->getMessage()]);
        }

        return redirect()->route('admin.tax.classes.index')->with('success', 'Tax class updated.');
    }
}
