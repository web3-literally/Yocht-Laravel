<?php

namespace App\Widgets;

use Arrilot\Widgets\AbstractWidget;

/**
 * Class Map
 * @package App\Widgets
 */
class Map extends AbstractWidget
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
        $this->config['lat'] = doubleval($this->config['lat'] ?? 0);
        $this->config['lng'] = doubleval($this->config['lng'] ?? 0);
        $this->config['address'] = $this->config['address'] ?? '';
        $this->config['address'] = trim(str_replace(["\r", "\n"], ' ', strip_tags($this->config['address'])));
        if (!($this->config['address'] || ($this->config['lat'] && $this->config['lng']))) {
            return '';
        }

        $this->config['id'] = $this->config['id'] ?? uniqid('map_');
        $this->config['class'] = $this->config['class'] ?? '';
        $this->config['width'] = $this->config['width'] ?? '100%';
        $this->config['height'] = $this->config['height'] ?? '450px';
        $this->config['zoom'] = $this->config['zoom'] ?? null;
        $this->config['key'] = config('services.google_map.key');

        return view('widgets.map', [
            'config' => $this->config,
        ]);
    }
}
