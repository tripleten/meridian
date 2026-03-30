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
use Meridian\CmsSeo\Application\Commands\CreateCmsPageCommand;
use Meridian\CmsSeo\Application\Commands\CreateCmsPageHandler;
use Meridian\CmsSeo\Application\Commands\UpdateCmsPageCommand;
use Meridian\CmsSeo\Application\Commands\UpdateCmsPageHandler;
use Meridian\CmsSeo\Application\DTOs\CmsPageData;
use Meridian\CmsSeo\Application\Queries\GetCmsPageHandler;
use Meridian\CmsSeo\Application\Queries\GetCmsPageQuery;
use Meridian\CmsSeo\Application\Queries\ListCmsPagesHandler;
use Meridian\CmsSeo\Application\Queries\ListCmsPagesQuery;
use Meridian\CmsSeo\Domain\Repositories\CmsPageRepositoryInterface;
use Meridian\Shared\Domain\Exceptions\DomainException;

final class CmsPageController
{
    public function index(Request $request, ListCmsPagesHandler $handler): Response
    {
        $pages = $handler->handle(new ListCmsPagesQuery(
            search:  $request->string('search')->trim()->value(),
            state:   $request->string('state')->value(),
            perPage: 20,
        ));

        return Inertia::render('admin/cms-pages/index', [
            'pages'        => $pages,
            'filters'      => $request->only('search', 'state'),
            'stateOptions' => $this->stateOptions(),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('admin/cms-pages/create', [
            'stateOptions' => $this->stateOptions(),
        ]);
    }

    public function store(Request $request, CreateCmsPageHandler $handler): RedirectResponse
    {
        $validated = $request->validate([
            'title'               => ['required', 'string', 'max:500'],
            'url_key'             => ['required', 'string', 'max:255', 'regex:/^[a-z0-9\-\/]+$/'],
            'content'             => ['nullable', 'string'],
            'state'               => ['nullable', 'string', 'in:draft,published,archived'],
            'meta_title'          => ['nullable', 'string', 'max:255'],
            'meta_description'    => ['nullable', 'string', 'max:500'],
            'meta_keywords'       => ['nullable', 'string', 'max:500'],
            'meta_robots_noindex' => ['nullable', 'boolean'],
        ]);

        try {
            $handler->handle(new CreateCmsPageCommand(
                title:               $validated['title'],
                url_key:             $validated['url_key'],
                content:             $validated['content'] ?? null,
                state:               $validated['state'] ?? 'draft',
                meta_title:          $validated['meta_title'] ?? null,
                meta_description:    $validated['meta_description'] ?? null,
                meta_keywords:       $validated['meta_keywords'] ?? null,
                meta_robots_noindex: (bool) ($validated['meta_robots_noindex'] ?? false),
                channel_id:          null,
            ));
        } catch (DomainException $e) {
            return back()->withErrors(['url_key' => $e->getMessage()]);
        }

        return redirect()
            ->route('admin.cms-pages.index')
            ->with('success', 'CMS page created.');
    }

    public function edit(string $cmsPage, GetCmsPageHandler $handler): Response
    {
        return Inertia::render('admin/cms-pages/edit', [
            'page'         => CmsPageData::fromModel($handler->handle(new GetCmsPageQuery($cmsPage))),
            'stateOptions' => $this->stateOptions(),
        ]);
    }

    public function update(
        string $cmsPage,
        Request $request,
        UpdateCmsPageHandler $handler,
    ): RedirectResponse {
        $validated = $request->validate([
            'title'               => ['required', 'string', 'max:500'],
            'url_key'             => ['required', 'string', 'max:255', 'regex:/^[a-z0-9\-\/]+$/'],
            'content'             => ['nullable', 'string'],
            'state'               => ['nullable', 'string', 'in:draft,published,archived'],
            'meta_title'          => ['nullable', 'string', 'max:255'],
            'meta_description'    => ['nullable', 'string', 'max:500'],
            'meta_keywords'       => ['nullable', 'string', 'max:500'],
            'meta_robots_noindex' => ['nullable', 'boolean'],
        ]);

        try {
            $handler->handle(new UpdateCmsPageCommand(
                pageId:              $cmsPage,
                title:               $validated['title'],
                url_key:             $validated['url_key'],
                content:             $validated['content'] ?? null,
                state:               $validated['state'] ?? 'draft',
                meta_title:          $validated['meta_title'] ?? null,
                meta_description:    $validated['meta_description'] ?? null,
                meta_keywords:       $validated['meta_keywords'] ?? null,
                meta_robots_noindex: (bool) ($validated['meta_robots_noindex'] ?? false),
                channel_id:          null,
            ));
        } catch (DomainException $e) {
            return back()->withErrors(['url_key' => $e->getMessage()]);
        }

        return redirect()
            ->route('admin.cms-pages.index')
            ->with('success', 'CMS page updated.');
    }

    public function destroy(string $cmsPage, CmsPageRepositoryInterface $pages): RedirectResponse
    {
        $pages->delete($cmsPage);

        return redirect()
            ->route('admin.cms-pages.index')
            ->with('success', 'CMS page deleted.');
    }

    private function stateOptions(): array
    {
        return [
            ['value' => 'draft',     'label' => 'Draft'],
            ['value' => 'published', 'label' => 'Published'],
            ['value' => 'archived',  'label' => 'Archived'],
        ];
    }
}
