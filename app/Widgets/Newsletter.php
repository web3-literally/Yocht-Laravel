<?php

namespace App\Widgets;

use Arrilot\Widgets\AbstractWidget;
use Illuminate\Http\Request;
use Spatie\Newsletter\Newsletter as Mailchimp;
use Illuminate\Support\Facades\Log;

/**
 * Class Newsletter
 * @package App\Widgets
 */
class Newsletter extends AbstractWidget
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
     *
     * @param Mailchimp $mailchimp
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Exception
     */
    public function run(Mailchimp $mailchimp, Request $request)
    {
        if (env('MAILCHIMP_APIKEY')) {
            return view('widgets.newsletter', [
                'config' => $this->config,
            ]);
        }

        Log::alert('Mailchimp API key is missing.');
    }
}
