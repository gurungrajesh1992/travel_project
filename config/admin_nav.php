<?php

/*
|--------------------------------------------------------------------------
| Admin sidebar navigation
|--------------------------------------------------------------------------
|
| One entry per module from the requirement doc (§6). `route` is null for
| modules not yet built this pass — they render disabled in the sidebar so
| the full information architecture is visible without dead links. Wire
| up `route` (and `permission` if it should be gated) as each module is
| built via the `module:make` scaffold.
|
*/

return [
    'Catalog' => [
        ['label' => 'Destinations', 'route' => 'admin.destinations.index', 'permission' => 'manage destinations'],
        ['label' => 'Categories', 'route' => 'admin.categories.index', 'permission' => 'manage categories'],
        ['label' => 'Difficulty Levels', 'route' => 'admin.difficulty-levels.index', 'permission' => 'manage difficulty levels'],
        ['label' => 'Tours', 'route' => 'admin.tours.index', 'permission' => 'manage tours'],
        ['label' => 'Guides', 'route' => 'admin.guides.index', 'permission' => 'manage guides'],
        ['label' => 'Coupons', 'route' => 'admin.coupons.index', 'permission' => 'manage coupons'],
    ],
    'Sales' => [
        ['label' => 'Bookings', 'route' => 'admin.bookings.index', 'permission' => 'manage bookings'],
        ['label' => 'Inquiries', 'route' => 'admin.inquiries.index', 'permission' => 'manage inquiries'],
        ['label' => 'Reviews', 'route' => 'admin.reviews.index', 'permission' => 'manage reviews'],
        [
            'label' => 'Reports',
            'permission' => 'view reports',
            'children' => [
                ['label' => 'Sales & Revenue', 'route' => 'admin.reports.sales'],
                ['label' => 'Popular Tours', 'route' => 'admin.reports.tours'],
                ['label' => 'Monthly / Yearly Bookings', 'route' => 'admin.reports.bookings'],
                ['label' => 'Customers', 'route' => 'admin.reports.customers'],
                ['label' => 'Cancellations', 'route' => 'admin.reports.cancellations'],
            ],
        ],
        ['label' => 'Calendar', 'route' => 'admin.calendar.index', 'permission' => 'manage bookings'],
    ],
    'Content' => [
        [
            'label' => 'Blog',
            'permission' => 'manage blogs',
            'children' => [
                ['label' => 'Posts', 'route' => 'admin.blog-posts.index'],
                ['label' => 'Categories', 'route' => 'admin.blog-categories.index'],
            ],
        ],
        ['label' => 'Pages', 'route' => 'admin.pages.index', 'permission' => 'manage pages'],
        [
            'label' => 'Gallery',
            'permission' => 'manage gallery',
            'children' => [
                ['label' => 'Items', 'route' => 'admin.gallery-items.index'],
                ['label' => 'Categories', 'route' => 'admin.gallery-categories.index'],
            ],
        ],
        [
            'label' => 'FAQ',
            'permission' => 'manage faqs',
            'children' => [
                ['label' => 'Questions', 'route' => 'admin.faqs.index'],
                ['label' => 'Categories', 'route' => 'admin.faq-categories.index'],
            ],
        ],
        ['label' => 'Newsletter', 'route' => 'admin.newsletter.index', 'permission' => 'manage newsletter'],
    ],
    'System' => [
        ['label' => 'Customers', 'route' => 'admin.customers.index', 'permission' => 'manage customers'],
        ['label' => 'Roles & Permissions', 'route' => null, 'permission' => 'manage roles'],
        ['label' => 'Company Info', 'route' => 'admin.settings.company', 'permission' => 'manage settings'],
        ['label' => 'Theme Settings', 'route' => 'admin.settings.theme', 'permission' => 'manage settings'],
    ],
];
