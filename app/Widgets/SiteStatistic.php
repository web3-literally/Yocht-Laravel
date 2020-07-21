<?php

namespace App\Widgets;

use Arrilot\Widgets\AbstractWidget;

/**
 * Class SiteStatistic
 * @package App\Widgets
 */
class SiteStatistic extends AbstractWidget
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
        return view('widgets.site_statistic', [
            'config' => $this->config,
        ]);
    }
}
