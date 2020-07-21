<?php

namespace App\Widgets;

use Arrilot\Widgets\AbstractWidget;
use LaravelLocalization;

/**
 * Class LanguageSwitcher
 * @package App\Widgets
 */
class LanguageSwitcher extends AbstractWidget
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
        $supportedLocales = LaravelLocalization::getSupportedLocales();

        return view('widgets.language_switcher', [
            'config' => $this->config,
            'supportedLocales' => $supportedLocales,
            'currentLocaleName' => LaravelLocalization::getCurrentLocaleName(),
        ]);
    }
}
