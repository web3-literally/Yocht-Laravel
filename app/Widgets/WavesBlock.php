<?php

namespace App\Widgets;

use Arrilot\Widgets\AbstractWidget;

/**
 * Class WavesBlock
 * @package App\Widgets
 */
class WavesBlock extends AbstractWidget
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
        return view('widgets.waves_block', [
            'config' => $this->config,
        ]);
    }
}
