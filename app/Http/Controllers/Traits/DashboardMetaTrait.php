<?php

namespace App\Http\Controllers\Traits;

/**
 * Trait DashboardMetaTrait
 * @package App\Http\Controllers\Traits
 */
trait DashboardMetaTrait
{
    /**
     * @param string $title
     */
    public function setDashboardTitle($title = '')
    {
        $parts = [];
        if ($title) {
            $parts[] = $title;
        }
        $parts[] = trans('general.dashboard');
        resolve('seotools')->metatags()->setTitle(implode(config('seotools.meta.defaults.separator'), $parts));
    }

    /**
     * @param string $title
     */
    public function setVesselDashboardTitle($title = '')
    {
        $parts = [];
        if ($title) {
            $parts[] = $title;
        }
        $parts[] = trans('general.vessel_dashboard');
        resolve('seotools')->metatags()->setTitle(implode(config('seotools.meta.defaults.separator'), $parts));
    }
}