<?php

namespace App\Widgets;

use Arrilot\Widgets\AbstractWidget;

/**
 * Class SearchLocationForm
 * @package App\Widgets
 */
class SearchLocationForm extends AbstractWidget
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
        return view('widgets.search_location_form', [
            'config' => $this->config,
        ]);
    }
}
