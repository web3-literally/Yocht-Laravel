<?php

namespace App\Widgets;

use Arrilot\Widgets\AbstractWidget;
use Route;
use Menu;
use Sentinel;

/**
 * Class MainMenu
 * @package App\Widgets
 */
class MainMenu extends AbstractWidget
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

        $menu = \App\Models\Menu::find(1);
        if (is_null($menu)) {
            throw new \Exception('Main menu should exists.');
        }

        Menu::make('MainMenu', function ($mainmenu) use ($menu, $group) {
            $items = $menu->items()->orderBy('parent', 'asc')->orderBy('order', 'asc')->get();
            if ($items) {
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

                    // Check visibility of children
                    if (!empty($item->visible_for)) {
                        $children = $item->children()->where(function($q) use ($group) {
                            $q->where('visible_for', 'like', '%"'.$group.'"%')->orWhere('visible_for', '[]');
                        })->get();
                    } else {
                        $children = $item->children;
                    }

                    if ($children->count()) {
                        $options['class'] = ((isset($options['class']) && $options['class']) ? $options['class'] . ' ' : '') . 'nav-item dropdown';
                        $menuitem = $mainmenu->add($item->getTitle(), $options);
                        $menuitem->link->attr(['role' => 'button', 'class' => 'nav-link dropdown-toggle', 'data-toggle' => 'dropdown', 'aria-haspopup' => 'true', 'aria-expanded' => 'false']);
                    } else {
                        $options['class'] = ((isset($options['class']) && $options['class']) ? $options['class'] . ' ' : '') . 'nav-item';
                        $menuitem = $mainmenu->add($item->getTitle(), $options);
                        $menuitem->link->attr(['class' => 'nav-link']);
                    }

                    if (trim(strip_tags($item->content))) {
                        $content = '<div class="item-content">' . $item->content . '</div>';
                        $menuitem->after($content);
                    }

                    $menuitem->data('position', $item->order);
                }
            }
        })->sortBy('position', 'asc');

        return view('widgets.main_menu', [
            'config' => $this->config
        ]);
    }
}
