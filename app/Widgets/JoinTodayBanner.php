<?php

namespace App\Widgets;

use Arrilot\Widgets\AbstractWidget;

/**
 * Class JoinTodayBanner
 * @package App\Widgets
 */
class JoinTodayBanner extends AbstractWidget
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
        return view('widgets.join_today_banner', [
            'config' => $this->config,
        ]);
    }
}
