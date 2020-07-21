<?php

namespace App\Widgets;

use Arrilot\Widgets\AbstractWidget;

/**
 * Class NewsletterBanner
 * @package App\Widgets
 */
class NewsletterBanner extends AbstractWidget
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
        return view('widgets.newsletter_banner', [
            'config' => $this->config,
        ]);
    }
}
