<?php

namespace App\Widgets;

use Arrilot\Widgets\AbstractWidget;

/**
 * Class SearchForm
 * @package App\Widgets
 */
class SearchForm extends AbstractWidget
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
        return view('widgets.search_form', [
            'config' => $this->config,
        ]);
    }
}
