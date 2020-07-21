<?php

namespace App\Widgets;

use Arrilot\Widgets\AbstractWidget;
use Illuminate\Database\Eloquent\Collection;
use Route;
use Menu;
use Sentinel;

/**
 * Class FooterMenu
 * @package App\Widgets
 */
class FooterMenu extends AbstractWidget
{
    /**
     * The configuration array.
     *
     * @var array
     */
    protected $config = [];

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Exception
     */
    public function run()
    {
        $group = Sentinel::check() ? Sentinel::getUser()->getAccountType() : 'guest';

        $menu = \App\Models\Menu::find(2);
        if (is_null($menu)) {
            throw new \Exception('Footer menu should exists.');
        }

        $topItems = $menu->children()->orderBy('order', 'asc')->limit(6)->get();
        if ($topItems) {
            foreach ($topItems as $i => $topItem) {
                $childItems = $topItem->children()->orderBy('order', 'asc')->get();
                $topItem->parent = null;

                $items = new Collection();
                $items->add($topItem);
                if ($childItems) {
                    foreach ($childItems as $childItem) {
                        $items->add($childItem);
                    }
                }

                Menu::make('FooterMenu' . ($i + 1), function ($mainmenu) use ($items, $group) {
                    foreach ($items as $item) {
                        // Check visibility
                        if (!empty($item->visible_for)) {
                            if (!in_array($group, $item->visible_for)) {
                                continue;
                            }
                        }

                        $options = [
                            'id' => $item->id
                        ];
                        if (!is_null($item->parent)) {
                            $options['parent'] = $item->parent;
                        }
                        if ($item->link) {
                            if (Route::has($item->link)) {
                                $options['route'] = $item->link;
                            } else {
                                $options['url'] = $item->link;
                            }
                        } else {
                            $options['url'] = '#';
                        }
                        if ($item->html_class) {
                            $options['class'] = implode(' ', explode(',', $item->html_class));
                        }
                        $options['class'] = ((isset($options['class']) && $options['class']) ? $options['class'] . ' ' : '') . 'nav-item';
                        $menuitem = $mainmenu->add($item->getTitle(), $options);
                        $menuitem->link->attr(['class' => 'nav-link']);
                        $menuitem->data('position', $item->order);
                    }
                })->sortBy('position', 'asc');
            }
        }

        return view('widgets.footer_menu', [
            'config' => $this->config,
        ]);
    }
}
