<?php

namespace App\Widgets;

use Arrilot\Widgets\AbstractWidget;

/**
 * Class MainSlider
 * @package App\Widgets
 */
class MainSlider extends AbstractWidget
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
        return view('widgets.main_slider', [
            'config' => $this->config,
        ]);
    }
}
