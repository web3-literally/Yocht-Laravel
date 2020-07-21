<?php

namespace App;

/**
 * Class StandardPermissions
 * @package App
 */
class StandardPermissions extends \Cartalyst\Sentinel\Permissions\StandardPermissions
{
    /**
     * @var array
     */
    protected $secondaryPermissions = [
        'admin',
        'dev',
        'audit',
        'seo',
        'content',
        //'shop',
        /* Admin */
        'admin.users',
        'admin.users.groups',
        'admin.menus',
        'admin.pages',
        'admin.news',
        'admin.news.sources',
        'admin.blog',
        'admin.jobs',
        'admin.events',
        'admin.classifieds',
        'admin.vessels',
        'admin.services',
        'admin.reviews',
        'admin.billing',
        /* Members */
        'billing.payment-methods',
        'billing.subscriptions',
        'billing.invoices',
        'profile.services',
        'profile.video',
        'profile.service-areas',
        'messages.new',
        'accounts.manage',
        'members.favorites',
        'jobs.manage',
        'jobs.favorites',
        'events.manage',
        'events.favorites',
        'classifieds.manage',
        'classifieds.favorites',
        'tasks.manage',
        'business.manage',
        'business.favorites',
        'vessels.manage',
        'vessels.documents',
        'vessels.set-location',
        'vessels.crew',
        'vessels.transfer',
        'vessels.colors',
        'vessels.favorites',
        'related.notifications',
        'related.messages',
        'crew.manage',
        'crew.colors',
        'employees.manage',
        'employees.manage-salesman',
        'tickets.listing',
        'assigned.vessels',
        'assigned.crew',
        //'services.manage',
    ];
}