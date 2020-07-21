<?php

namespace App\Widgets;

use Arrilot\Widgets\AbstractWidget;
use SEO;

/**
 * Class SocialShare
 * @package App\Widgets
 */
class SocialShare extends AbstractWidget
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
        $fullTitle = SEO::metatags()->getTitle();
        $title = current(explode(config('seotools.meta.defaults.separator'), $fullTitle));

        $description = SEO::metatags()->getDescription();

        return view('widgets.social_share', [
            'config' => $this->config,
            'title' => $title,
            'description' => $description
        ]);
    }
}
