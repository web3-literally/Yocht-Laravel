<?php

namespace App\Widgets;

use Arrilot\Widgets\AbstractWidget;

/**
 * Class Copyright
 * @package App\Widgets
 */
class Copyright extends AbstractWidget
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
        return view('widgets.copyright', [
            'config' => $this->config,
        ]);
    }
}
