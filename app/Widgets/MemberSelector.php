<?php

namespace App\Widgets;

use Arrilot\Widgets\AbstractWidget;
use Illuminate\Support\Collection;
use Cookie;

class MemberSelector extends AbstractWidget
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
        $visibility = empty(Cookie::get('job_visibility')) ? 'private' : Cookie::get('job_visibility');
        $members = new Collection(Cookie::get('selected_members') ? json_decode(Cookie::get('selected_members')) : []);

        return view('widgets.member_selector', [
            'config' => $this->config,
            'visibility' => $visibility,
            'members' => $members,
        ]);
    }
}
