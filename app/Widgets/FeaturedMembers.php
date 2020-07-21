<?php

namespace App\Widgets;

use App\User;
use Arrilot\Widgets\AbstractWidget;
use Cache;

/**
 * Class FeaturedMembers
 * @package App\Widgets
 */
class FeaturedMembers extends AbstractWidget
{
    /**
     * The configuration array.
     *
     * @var array
     */
    protected $config = [];

    /**
     * Treat this method as a controller action.
     * Return view() or other content to display.
     */
    public function run()
    {
        $members = Cache::remember('FeatureMembers', 10, function () {
            return User::members(['business'])->inRandomOrder()->limit(18)->get();
        });

        return view('widgets.featured_members', [
            'config' => $this->config,
            'members' => $members,
        ]);
    }
}
