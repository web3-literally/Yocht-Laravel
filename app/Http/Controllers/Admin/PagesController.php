<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Traits\SeoMetaTrait;
use App\Http\Controllers\Controller;
use App\Http\Requests\PageRequest;
use App\Page;
use Cartalyst\Sentinel\Sentinel;
use Response;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\URL;
use App\Http\Controllers\Admin\Traits\CKEditorImageUploadTrait;

/**
 * Class PagesController
 * @package App\Http\Controllers\Admin
 */
class PagesController extends Controller
{
    use CKEditorImageUploadTrait;
    use SeoMetaTrait;

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        //$pages = Page::orderBy('title', 'asc')->take(10)->get();
        $pages = [];
        $layouts = \App\Helpers\Pages::getPageLayouts();

        return view('admin.pages.index', compact('pages', 'layouts'));
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        $layouts = \App\Helpers\Pages::getPageLayouts();
        $layouts['default'] = trans('pages/form.default_layout');

        return view('admin.pages.create', compact('layouts'));
    }

    /**
     * @param PageRequest $request
     * @param Sentinel $sentinel
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(PageRequest $request, Sentinel $sentinel)
    {
        libxml_use_internal_errors(true);

        $page = new Page($request->except(['files', 'user_id', 'meta']));
        $page->user_id = $sentinel->getUser()->getUserId();

        $page->save();

        $this->updateSeoData($page, $request);

        if ($page->id) {
            return redirect('admin/pages')->with('success', trans('pages/message.success.create'));
        } else {
            return redirect('admin/pages')->withInput()->with('error', trans('pages/message.error.create'));
        }
    }

    /**
     * @param Page $page
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(Page $page)
    {
        $layouts = \App\Helpers\Pages::getPageLayouts();
        $layouts['default'] = trans('pages/form.default_layout');

        return view('admin.pages.edit', compact('page', 'layouts'));
    }

    /**
     * @param PageRequest $request
     * @param Page $page
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(PageRequest $request, Page $page)
    {
        libxml_use_internal_errors(true);

        if ($page->update($request->except(['files', 'user_id', 'close', 'meta']))) {
            $this->updateSeoData($page, $request);

            $redirect = redirect();
            $redirect = $request->exists('close') ? $redirect->to('admin/pages') : $redirect->back();
            return $redirect->with('success', trans('pages/message.success.update'));
        } else {
            return redirect()->back()->withInput()->with('error', trans('pages/message.error.update'));
        }
    }

    /**
     * @param Page $page
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function destroy(Page $page)
    {
        if ($page->delete()) {
            return redirect('admin/pages')->with('success', trans('pages/message.success.delete'));
        } else {
            return redirect('admin/pages/index')->with('error', trans('pages/message.error.delete'));
        }
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    public function data()
    {
        $layouts = \App\Helpers\Pages::getPageLayouts();

        $pages = Page::get(['id', 'title', 'slug', 'layout', 'updated_at', 'created_at']);

        return DataTables::of($pages)->addColumn('title_link', function (Page $page) {
            return '<a href=' . URL::to('admin/pages/' . $page->id . '/edit') . '>' . htmlspecialchars($page->title) . '</a>';
        })->editColumn('layout', function (Page $page) use ($layouts) {
            return $layouts[$page->layout];
        })->editColumn('updated_at', function (Page $page) {
            return $page->updated_at->diffForHumans();
        })->editColumn('created_at', function (Page $page) {
            return $page->created_at->toFormattedDateString();
        })->addColumn('actions', function (Page $page) {
            $actions = '<a href=' . URL::to('admin/pages/' . $page->id . '/edit') . '><i class="livicon" data-name="edit" data-size="18" data-loop="true" data-c="#428BCA" data-hc="#428BCA" title="' . trans('pages/table.update-page') . '"></i></a>';
            $actions .= '<a href=' . route('admin.pages.confirm-delete', $page->id) . ' data-toggle="modal" data-id="' . $page->id . '" data-target="#delete_confirm"><i class="livicon" data-name="trash" data-size="18" data-loop="true" data-c="#f56954" data-hc="#f56954" title="' . trans('pages/table.delete-page') . '"></i></a>';
            return $actions;
        })->rawColumns(['title_link', 'actions'])->make(true);
    }
}
