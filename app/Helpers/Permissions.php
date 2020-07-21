<?php

namespace App\Helpers;

use App\StandardPermissions;
use Sentinel;

/**
 * Class Permissions
 * @package App\Helpers
 */
class Permissions {
    /**
     * @param $needle
     * @param $haystack
     * @param string $currentKey
     * @return bool|string
     */
    protected static function recursive_array_search($needle, $haystack, $currentKey = '')
    {
        foreach ($haystack as $key => $value) {
            if (is_array($value)) {
                $nextKey = self::recursive_array_search($needle, $value, $currentKey . '[' . $key . ']');
                if ($nextKey) {
                    return $nextKey;
                }
            } else if ($value == $needle) {
                return is_numeric($key) ? $currentKey . '[' . $key . ']' : $currentKey . '["' . $key . '"]';
            }
        }

        return false;
    }

    /**
     * Permissions group labels
     *
     * @return array
     */
    public static function getGroups()
    {
        return [
            'other' => trans('permissions.groups.other'),
            'backend' => trans('permissions.group.backend'),
            'owner' => trans('permissions.group.owner'),
            'marine' => trans('permissions.group.marine'),
        ];
    }

    /**
     * Used to display permissions in admin panel
     *
     * @param null|string $key
     * @return array
     */
    public static function getGroupPermissions(string $key = null)
    {
        $data = [
            'backend' => [
                'admin',
                'dev',
                'audit',
                'seo',
                'content',
                //'shop',
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
                'admin.billing'
            ],
            'billing' => [
                'billing.payment-methods',
                'billing.subscriptions',
                'billing.invoices'
            ],
            'profile' => [
                'profile.services',
                'profile.video',
                'profile.service-areas',
            ],
            'messages' => [
                'messages.new'
            ],
            'accounts' => [
                'accounts.manage',
            ],
            'members' => [
                'members.favorites'
            ],
            'jobs' => [
                'jobs.manage',
                'jobs.favorites'
            ],
            'tickets' => [
                'tickets.listing'
            ],
            'events' => [
                'events.manage',
                'events.favorites'
            ],
            'classifieds' => [
                'classifieds.manage',
                'classifieds.favorites'
            ],
            'tasks' => [
                'tasks.manage',
            ],
            'business' => [
                'business.manage',
                'business.favorites',
            ],
            'vessels' => [
                'vessels.manage',
                'vessels.documents',
                'vessels.set-location',
                'vessels.crew',
                'vessels.transfer',
                'vessels.colors',
                'vessels.favorites'
            ],
            'related' => [
                'related.notifications',
                'related.messages',
            ],
            'crew' => [
                'crew.manage',
                'crew.colors',
            ],
            'employees' => [
                'employees.manage',
                'employees.manage-salesman',
            ],
            'assigned' => [
                'assigned.vessels',
                'assigned.crew',
            ],
            'other' => []
        ];

        foreach(self::getStandard() as $id) {
            if (!self::recursive_array_search($id, $data)) {
                $data['other'][] = $id;
            }
        }

        if (!is_null($key) && isset($data[$key])) {
            return $data[$key];
        }

        return $data;
    }

    /**
     * Used to display permissions in admin panel
     *
     * @param array $except
     * @return array
     */
    public static function getGroupPermissionsExcept(array $except = []) {
        $data = self::getGroupPermissions();
        if ($except) {
            foreach($except as $id) {
                if (isset($data[$id])) {
                    unset($data[$id]);
                }
            }
        }

        return $data;
    }

    /**
     * @return array
     */
    public static function getStandard() {
        $standardPermissions =  new StandardPermissions();
        return $standardPermissions->getSecondaryPermissions();
    }

    /**
     * @return bool
     */
    public static function canContactTo()
    {
        return (Sentinel::check() && in_array(Sentinel::getUser()->getAccountType(), ['owner', 'marine', 'marinas_shipyards', 'captain']));
    }

    /**
     * @return bool
     */
    public static function canSendReview()
    {
        return (Sentinel::check() && in_array(Sentinel::getUser()->getAccountType(), ['owner', 'marine', 'marinas_shipyards', 'captain']));
    }
}