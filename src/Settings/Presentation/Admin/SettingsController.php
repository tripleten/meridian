<?php

declare(strict_types=1);

/**
 * This file is part of Meridian Ecommerce Platform.
 *
 * @package    Meridian\Settings\Presentation\Admin
 * @author     L K Lalitesh <lalitesh@live.com>
 * @company    Bytics Lab
 * @copyright  2026 Bytics Lab. All rights reserved.
 */

namespace Meridian\Settings\Presentation\Admin;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Meridian\Settings\Application\Commands\UpdateSettingsCommand;
use Meridian\Settings\Application\Commands\UpdateSettingsHandler;
use Meridian\Settings\Infrastructure\Persistence\EloquentSetting;

final class SettingsController
{
    private const GROUPS = [
        'general' => 'General',
        'seo'     => 'SEO',
        'social'  => 'Social',
        'scripts' => 'Scripts',
        'gdpr'    => 'GDPR',
    ];

    public function index(): Response
    {
        return $this->showGroup('general');
    }

    public function show(string $group): Response
    {
        return $this->showGroup($group);
    }

    public function update(string $group, Request $request, UpdateSettingsHandler $handler): RedirectResponse
    {
        $values = $request->except('_token', '_method');

        $handler->handle(new UpdateSettingsCommand(
            group:     $group,
            values:    $values,
            updatedBy: auth()->id(),
        ));

        return back()->with('success', 'Settings saved.');
    }

    private function showGroup(string $group): Response
    {
        $settings = EloquentSetting::where('group', $group)
            ->pluck('value', 'key')
            ->toArray();

        return Inertia::render('admin/settings/index', [
            'groups'        => self::GROUPS,
            'currentGroup'  => $group,
            'currentLabel'  => self::GROUPS[$group] ?? ucfirst($group),
            'settings'      => $settings,
            'groupFields'   => $this->groupFields($group),
        ]);
    }

    private function groupFields(string $group): array
    {
        return match($group) {
            'general' => [
                ['key' => 'store_name',        'label' => 'Store Name',        'type' => 'text'],
                ['key' => 'store_email',        'label' => 'Store Email',       'type' => 'email'],
                ['key' => 'store_phone',        'label' => 'Store Phone',       'type' => 'text'],
                ['key' => 'store_address',      'label' => 'Store Address',     'type' => 'textarea'],
                ['key' => 'default_currency',   'label' => 'Default Currency',  'type' => 'text'],
                ['key' => 'default_locale',     'label' => 'Default Locale',    'type' => 'text'],
                ['key' => 'items_per_page',     'label' => 'Items Per Page',    'type' => 'number'],
            ],
            'seo' => [
                ['key' => 'meta_title',         'label' => 'Default Meta Title',       'type' => 'text'],
                ['key' => 'meta_description',   'label' => 'Default Meta Description', 'type' => 'textarea'],
                ['key' => 'meta_keywords',      'label' => 'Meta Keywords',            'type' => 'text'],
                ['key' => 'robots',             'label' => 'Robots',                   'type' => 'text'],
                ['key' => 'canonical_url',      'label' => 'Canonical Base URL',       'type' => 'text'],
            ],
            'social' => [
                ['key' => 'facebook_url',   'label' => 'Facebook URL',   'type' => 'url'],
                ['key' => 'twitter_url',    'label' => 'Twitter/X URL',  'type' => 'url'],
                ['key' => 'instagram_url',  'label' => 'Instagram URL',  'type' => 'url'],
                ['key' => 'youtube_url',    'label' => 'YouTube URL',    'type' => 'url'],
                ['key' => 'linkedin_url',   'label' => 'LinkedIn URL',   'type' => 'url'],
            ],
            'scripts' => [
                ['key' => 'head_scripts',   'label' => 'Head Scripts (before </head>)',    'type' => 'code'],
                ['key' => 'body_scripts',   'label' => 'Body Scripts (after <body>)',       'type' => 'code'],
                ['key' => 'footer_scripts', 'label' => 'Footer Scripts (before </body>)',  'type' => 'code'],
                ['key' => 'gtm_id',         'label' => 'Google Tag Manager ID',            'type' => 'text'],
                ['key' => 'ga4_id',         'label' => 'Google Analytics 4 ID',            'type' => 'text'],
            ],
            'gdpr' => [
                ['key' => 'cookie_banner_enabled', 'label' => 'Cookie Banner Enabled',   'type' => 'boolean'],
                ['key' => 'cookie_banner_text',    'label' => 'Cookie Banner Text',       'type' => 'textarea'],
                ['key' => 'privacy_page_id',       'label' => 'Privacy Policy Page ID',   'type' => 'text'],
                ['key' => 'terms_page_id',         'label' => 'Terms & Conditions Page',  'type' => 'text'],
            ],
            default => [],
        };
    }
}
