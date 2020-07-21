<?php

namespace App\Http\Controllers;

use App\Page;
use Response;
use Sentinel;
use View;

/**
 * Class PagesController
 * @package App\Http\Controllers
 */
class PagesController extends Controller
{
    /**
     * @param string $slug
     * @return \Illuminate\View\View
     */
    public function showPage($slug = '')
    {
        $page = Page::where('slug', $slug)->first();
        if ($page) {
            $page->seoable();
            $layout = 'layouts/page/' . $page->layout ?? 'default';
            return view('page', compact('page', 'layout'));
        } elseif(View::exists($slug)) {
            return view($slug);
        }

        abort('404');
    }
}
