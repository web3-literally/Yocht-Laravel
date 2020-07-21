<?php

namespace App\Widgets;

use Arrilot\Widgets\AbstractWidget;
use Setting;

/**
 * Class FollowUs
 * @package App\Widgets
 */
class FollowUs extends AbstractWidget
{
    /**
     * The configuration array.
     *
     * @var array
     */
    protected $config = [];

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function run()
    {
        $links = Setting::get('social_links');

        return view('widgets.follow_us', [
            'config' => $this->config,
            'links' => $links,
        ]);
    }
}
