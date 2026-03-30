<?php

declare(strict_types=1);

/**
 * This file is part of Meridian Ecommerce Platform.
 *
 * @package    Meridian\Payments\Presentation\Admin
 * @author     L K Lalitesh <lalitesh@live.com>
 * @company    Bytics Lab
 * @copyright  2026 Bytics Lab. All rights reserved.
 */

namespace Meridian\Payments\Presentation\Admin;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Meridian\Payments\Application\Commands\UpdatePaymentMethodCommand;
use Meridian\Payments\Application\Commands\UpdatePaymentMethodHandler;
use Meridian\Payments\Application\DTOs\PaymentMethodData;
use Meridian\Payments\Application\Queries\ListPaymentMethodsHandler;
use Meridian\Payments\Application\Queries\ListPaymentMethodsQuery;
use Meridian\Payments\Infrastructure\Persistence\EloquentPaymentMethod;
use Meridian\Shared\Domain\Exceptions\DomainException;

final class PaymentMethodController
{
    public function index(ListPaymentMethodsHandler $handler): Response
    {
        $methods = $handler->handle(new ListPaymentMethodsQuery());

        return Inertia::render('admin/payments/methods/index', [
            'methods' => $methods->map(fn ($m) => PaymentMethodData::fromModel($m))->values()->all(),
        ]);
    }

    public function edit(string $method): Response
    {
        $model = EloquentPaymentMethod::findOrFail($method);

        return Inertia::render('admin/payments/methods/edit', [
            'method' => PaymentMethodData::fromModel($model),
        ]);
    }

    public function update(string $method, Request $request, UpdatePaymentMethodHandler $handler): RedirectResponse
    {
        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'is_active'   => ['required', 'boolean'],
            'sort_order'  => ['required', 'integer', 'min:0'],
            'config'      => ['nullable', 'array'],
        ]);

        try {
            $handler->handle(new UpdatePaymentMethodCommand(
                id:          $method,
                name:        $validated['name'],
                description: $validated['description'] ?? null,
                is_active:   (bool) $validated['is_active'],
                sort_order:  (int) $validated['sort_order'],
                config:      $validated['config'] ?? [],
            ));
        } catch (DomainException $e) {
            return back()->withErrors(['name' => $e->getMessage()]);
        }

        return redirect()->route('admin.payments.methods.index')->with('success', 'Payment method updated.');
    }
}
