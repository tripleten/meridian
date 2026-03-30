<?php

declare(strict_types=1);

/**
 * This file is part of Meridian Ecommerce Platform.
 *
 * @package    Meridian\Catalog\Presentation\Admin\Categories
 * @author     L K Lalitesh <lalitesh@live.com>
 * @company    Bytics Lab
 * @copyright  2026 Bytics Lab. All rights reserved.
 */

namespace Meridian\Catalog\Presentation\Admin\Categories;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Meridian\Catalog\Application\Commands\CreateCategoryCommand;
use Meridian\Catalog\Application\Commands\CreateCategoryHandler;
use Meridian\Catalog\Application\Commands\UpdateCategoryCommand;
use Meridian\Catalog\Application\Commands\UpdateCategoryHandler;
use Meridian\Catalog\Application\Queries\ListCategoriesHandler;
use Meridian\Catalog\Infrastructure\Persistence\EloquentCategory;
use Meridian\Shared\Domain\Exceptions\DomainException;

final class CategoryController
{
    public function index(ListCategoriesHandler $handler): Response
    {
        return Inertia::render('admin/categories/index', [
            'categories' => $handler->handle(),
        ]);
    }

    public function create(ListCategoriesHandler $handler): Response
    {
        return Inertia::render('admin/categories/create', [
            'parentOptions' => $handler->handle(),
        ]);
    }

    public function store(Request $request, CreateCategoryHandler $handler): RedirectResponse
    {
        $validated = $request->validate([
            'name'             => ['required', 'string', 'max:200'],
            'url_key'          => ['required', 'string', 'max:220', 'unique:categories,url_key', 'regex:/^[a-z0-9-\/]+$/'],
            'parent_id'        => ['nullable', 'string', 'exists:categories,id'],
            'description'      => ['nullable', 'string'],
            'is_active'        => ['boolean'],
            'meta_title'       => ['nullable', 'string', 'max:160'],
            'meta_description' => ['nullable', 'string', 'max:320'],
        ]);

        try {
            $handler->handle(new CreateCategoryCommand(
                name:             $validated['name'],
                url_key:          $validated['url_key'],
                parent_id:        $validated['parent_id'] ?? null,
                description:      $validated['description'] ?? null,
                is_active:        $validated['is_active'] ?? true,
                meta_title:       $validated['meta_title'] ?? null,
                meta_description: $validated['meta_description'] ?? null,
            ));
        } catch (DomainException $e) {
            return back()->withErrors(['name' => $e->getMessage()]);
        }

        return redirect()->route('admin.categories.index')->with('success', 'Category created.');
    }

    public function edit(string $category, ListCategoriesHandler $handler): Response
    {
        $model = EloquentCategory::findOrFail($category);

        return Inertia::render('admin/categories/edit', [
            'category' => [
                'id'               => $model->id,
                'parent_id'        => $model->parent_id,
                'name'             => $model->name,
                'url_key'          => $model->url_key,
                'description'      => $model->description,
                'is_active'        => $model->is_active,
                'meta_title'       => $model->seo_title,
                'meta_description' => $model->seo_description,
            ],
            'parentOptions' => $handler->handle(),
        ]);
    }

    public function update(string $category, Request $request, UpdateCategoryHandler $handler): RedirectResponse
    {
        $validated = $request->validate([
            'name'             => ['required', 'string', 'max:200'],
            'url_key'          => ['required', 'string', 'max:220', "unique:categories,url_key,{$category}", 'regex:/^[a-z0-9-\/]+$/'],
            'parent_id'        => ['nullable', 'string', "not_in:{$category}", 'exists:categories,id'],
            'description'      => ['nullable', 'string'],
            'is_active'        => ['boolean'],
            'meta_title'       => ['nullable', 'string', 'max:160'],
            'meta_description' => ['nullable', 'string', 'max:320'],
        ]);

        try {
            $handler->handle(new UpdateCategoryCommand(
                categoryId:       $category,
                name:             $validated['name'],
                url_key:          $validated['url_key'],
                parent_id:        $validated['parent_id'] ?? null,
                description:      $validated['description'] ?? null,
                is_active:        $validated['is_active'] ?? true,
                meta_title:       $validated['meta_title'] ?? null,
                meta_description: $validated['meta_description'] ?? null,
            ));
        } catch (DomainException $e) {
            return back()->withErrors(['name' => $e->getMessage()]);
        }

        return redirect()->route('admin.categories.index')->with('success', 'Category updated.');
    }

    public function destroy(string $category): RedirectResponse
    {
        $model = EloquentCategory::findOrFail($category);

        if ($model->children()->exists()) {
            return back()->withErrors(['category' => 'Cannot delete a category that has sub-categories.']);
        }

        $model->delete();

        return redirect()->route('admin.categories.index')->with('success', 'Category deleted.');
    }
}
