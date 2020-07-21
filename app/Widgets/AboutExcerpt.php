<?php

namespace App\Widgets;

use Arrilot\Widgets\AbstractWidget;

/**
 * Class AboutExcerpt
 * @package App\Widgets
 */
class AboutExcerpt extends AbstractWidget
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
        return view('widgets.about_excerpt', [
            'config' => $this->config,
        ]);
    }
}
