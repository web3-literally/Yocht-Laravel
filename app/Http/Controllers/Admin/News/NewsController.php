<?php

namespace App\Http\Controllers\Admin\News;

use App\Http\Requests\NewsRequest;
use App\Repositories\NewsRepository;
use App\Http\Controllers\AppBaseController as InfyOmBaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;
use App\Models\News;
use Flash;
use Image;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use Yajra\DataTables\DataTables;

/**
 * Class NewsController
 * @package App\Http\Controllers\Admin\News
 */
class NewsController extends InfyOmBaseController
{
    /**
     * @var  NewsRepository
     */
    private $newsRepository;

    /**
     * NewsController constructor.
     * @param NewsRepository $newsRepo
     */
    public function __construct(NewsRepository $newsRepo)
    {
        $this->newsRepository = $newsRepo;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function index(Request $request)
    {
        $this->newsRepository->pushCriteria(new RequestCriteria($request));
        $news = $this->newsRepository->all();

        return view('admin.news.index')->with('news', $news);
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        return view('admin.news.create');
    }

    /**
     * @param NewsRequest $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws \Throwable
     */
    public function store(NewsRequest $request)
    {
        $news = new News($request->except('image'));

        if ($request->hasFile('image')) {
            $imageFileName = $this->processImage($request);
            if ($imageFileName) {
                $news->deleteImage(false);
                $news->image = $imageFileName;
            } else {
                $request->session()->flash('error', 'Failed to process image file.');
            }
        }

        if ($news->save()) {
            return redirect(route('admin.news.index'))->with('success', 'Post was successfully created.');
        } else {
            return redirect(route('admin.news.index'))->withInput()->with('error', 'There was an issue creating the post. Please try again.');
        }
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
     */
    public function show($id)
    {
        $news = $this->newsRepository->findWithoutFail($id);

        if (empty($news)) {
            Flash::error('News not found');

            return redirect(route('news.index'));
        }

        return view('admin.news.show')->with('news', $news);
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
     */
    public function edit($id)
    {
        $news = $this->newsRepository->findWithoutFail($id);

        if (empty($news)) {
            Flash::error('News not found');

            return redirect(route('news.index'));
        }

        return view('admin.news.edit')->with('news', $news);
    }

    /**
     * @param $id
     * @param NewsRequest $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update($id, NewsRequest $request)
    {
        $news = News::findOrFail($id);

        if ($request->hasFile('image')) {
            $imageFileName = $this->processImage($request);
            if ($imageFileName) {
                $news->deleteImage(false);
                $news->image = $imageFileName;
            } else {
                $request->session()->flash('error', 'Failed to process image file.');
            }
        }

        if ($news->update($request->except('image'))) {
            return redirect(route('admin.news.index'))->with('success', 'Post was successfully updated.');
        } else {
            return redirect(route('admin.news.index'))->withInput()->with('error', 'There was an issue updating the post. Please try again.');
        }
    }

    /**
     * @param null $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getModalDelete($id = null)
    {
        $error = '';
        $model = '';
        $confirm_route = route('admin.news.delete', ['id' => $id]);
        return View('admin.layouts/modal_confirmation', compact('error', 'model', 'confirm_route'));
    }

    /**
     * @param null $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function getDelete($id = null)
    {
        $sample = News::destroy($id);

        // Redirect to the group management page
        return redirect(route('admin.news.index'))->with('success', Lang::get('message.success.delete'));
    }

    /**
     * @param $id
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deleteImage($id, Request $request)
    {
        $news = News::find($id);

        if (empty($news)) {
            return abort(404);
        }

        $news->deleteImage();

        $request->session()->flash('success', 'Post image was successfully deleted.');

        return redirect(route('admin.news.edit', $news->id));
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    public function data()
    {
        $pages = News::get(['id', 'title', 'date']);

        return DataTables::of($pages)->editColumn('date', function (News $model) {
            return $model->date->toFormattedDateString();
        })->addColumn('actions', function (News $model) {
            $actions = '<a href=' . route('admin.news.edit', ['news' => $model->id]) . '><i class="livicon" data-name="edit" data-size="18" data-loop="true" data-c="#428BCA" data-hc="#428BCA" title="update post"></i></a>';
            $actions .= '<a href=' . route('admin.news.confirm-delete', ['id' => $model->id]) . ' data-toggle="modal" data-id="' . route('admin.news.delete', ['id' => $model->id]) . '" data-target="#delete_confirm"><i class="livicon" data-name="remove-alt" data-size="18" data-loop="true" data-c="#f56954" data-hc="#f56954" title="delete post"></i></a>';
            return $actions;
        })->rawColumns(['actions'])->make(true);
    }

    /**
     * @param Request $request
     * @return bool|null|string
     */
    protected function processImage(Request $request)
    {
        $defaultImgFormat = config('app.default_image_format');

        try {
            if ($request->hasFile('image')) {
                $file = $request->file('image');

                $extension = $file->extension();
                $hash = uniqid();
                $fileName = $hash . '.' . $extension;
                $destinationPath = public_path() . '/uploads/news/';

                $temp = $file->move($destinationPath, $fileName);

                if ($extension != $defaultImgFormat) {
                    $fileName = $hash . '.' . $defaultImgFormat;
                    Image::make($temp->getPathname())->encode($defaultImgFormat, 75)->save($destinationPath . $fileName);
                    unlink($temp);
                }

                return $fileName;
            }
        } catch (\Throwable $e) {
            return false;
        }

        return null;
    }
}
