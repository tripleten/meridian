<?php

declare(strict_types=1);

/**
 * This file is part of Meridian Ecommerce Platform.
 *
 * @package    Meridian\CmsSeo\Presentation\Admin
 * @author     L K Lalitesh <lalitesh@live.com>
 * @company    Bytics Lab
 * @copyright  2026 Bytics Lab. All rights reserved.
 */

namespace Meridian\CmsSeo\Presentation\Admin;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Meridian\CmsSeo\Application\Commands\CreateCmsBlockCommand;
use Meridian\CmsSeo\Application\Commands\CreateCmsBlockHandler;
use Meridian\CmsSeo\Application\Commands\UpdateCmsBlockCommand;
use Meridian\CmsSeo\Application\Commands\UpdateCmsBlockHandler;
use Meridian\CmsSeo\Application\DTOs\CmsBlockData;
use Meridian\CmsSeo\Application\Queries\ListCmsBlocksHandler;
use Meridian\CmsSeo\Domain\Repositories\CmsBlockRepositoryInterface;
use Meridian\Shared\Domain\Exceptions\DomainException;

final class CmsBlockController
{
    public function index(ListCmsBlocksHandler $handler): Response
    {
        return Inertia::render('admin/cms-blocks/index', [
            'blocks' => $handler->handle(),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('admin/cms-blocks/create');
    }

    public function store(Request $request, CreateCmsBlockHandler $handler): RedirectResponse
    {
        $validated = $request->validate([
            'identifier' => ['required', 'string', 'max:100', 'regex:/^[a-z0-9\-]+$/', 'unique:cms_blocks,identifier'],
            'title'      => ['required', 'string', 'max:255'],
            'content'    => ['nullable', 'string'],
            'is_active'  => ['boolean'],
        ]);

        try {
            $handler->handle(new CreateCmsBlockCommand(
                identifier: $validated['identifier'],
                title:      $validated['title'],
                content:    $validated['content'] ?? null,
                is_active:  (bool) ($validated['is_active'] ?? true),
                channel_id: null,
            ));
        } catch (DomainException $e) {
            return back()->withErrors(['identifier' => $e->getMessage()]);
        }

        return redirect()
            ->route('admin.cms-blocks.index')
            ->with('success', 'CMS block created.');
    }

    public function edit(string $cmsBlock, CmsBlockRepositoryInterface $blocks): Response
    {
        $block = $blocks->findById($cmsBlock);

        if ($block === null) {
            abort(404);
        }

        return Inertia::render('admin/cms-blocks/edit', [
            'block' => CmsBlockData::fromModel($block),
        ]);
    }

    public function update(
        string $cmsBlock,
        Request $request,
        UpdateCmsBlockHandler $handler,
    ): RedirectResponse {
        $validated = $request->validate([
            'identifier' => ['required', 'string', 'max:100', 'regex:/^[a-z0-9\-]+$/', "unique:cms_blocks,identifier,{$cmsBlock}"],
            'title'      => ['required', 'string', 'max:255'],
            'content'    => ['nullable', 'string'],
            'is_active'  => ['boolean'],
        ]);

        try {
            $handler->handle(new UpdateCmsBlockCommand(
                blockId:    $cmsBlock,
                identifier: $validated['identifier'],
                title:      $validated['title'],
                content:    $validated['content'] ?? null,
                is_active:  (bool) ($validated['is_active'] ?? true),
                channel_id: null,
            ));
        } catch (DomainException $e) {
            return back()->withErrors(['identifier' => $e->getMessage()]);
        }

        return redirect()
            ->route('admin.cms-blocks.index')
            ->with('success', 'CMS block updated.');
    }

    public function destroy(string $cmsBlock, CmsBlockRepositoryInterface $blocks): RedirectResponse
    {
        $blocks->delete($cmsBlock);

        return redirect()
            ->route('admin.cms-blocks.index')
            ->with('success', 'CMS block deleted.');
    }
}
