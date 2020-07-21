<?php

namespace App\Http\Controllers\Admin\News;

use App\Http\Requests\NewsSourceRequest;
use App\Repositories\NewsSourceRepository;
use App\Http\Controllers\AppBaseController as InfyOmBaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;
use App\Models\NewsSource;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class NewsSourceController
 * @package App\Http\Controllers\Admin\News
 */
class NewsSourceController extends InfyOmBaseController
{
    /**
     * @var  NewsSourceRepository
     */
    private $newsSourceRepository;

    /**
     * NewsSourceController constructor.
     * @param NewsSourceRepository $newsSourceRepo
     */
    public function __construct(NewsSourceRepository $newsSourceRepo)
    {
        $this->newsSourceRepository = $newsSourceRepo;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function index(Request $request)
    {

        $this->newsSourceRepository->pushCriteria(new RequestCriteria($request));
        $newsSources = $this->newsSourceRepository->all();
        return view('admin.news.sources.index')->with('newsSources', $newsSources);
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        return view('admin.news.sources.create');
    }

    /**
     * @param NewsSourceRequest $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function store(NewsSourceRequest $request)
    {
        $input = $request->all();

        $newsSource = $this->newsSourceRepository->create($input);

        Flash::success('News source saved successfully.');

        return redirect(route('admin.news.sources.index'));
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
     */
    public function edit($id)
    {
        $newsSource = $this->newsSourceRepository->findWithoutFail($id);

        if (empty($newsSource)) {
            Flash::error('News source not found');

            return redirect(route('admin.news.sources.index'));
        }

        return view('admin.news.sources.edit')->with('newsSource', $newsSource);
    }

    /**
     * @param $id
     * @param NewsSourceRequest $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function update($id, NewsSourceRequest $request)
    {
        $newsSource = $this->newsSourceRepository->findWithoutFail($id);

        if (empty($newsSource)) {
            Flash::error('News source not found');

            return redirect(route('admin.news.sources.index'));
        }

        $newsSource = $this->newsSourceRepository->update($request->all(), $id);

        Flash::success('News source updated successfully.');

        return redirect(route('admin.news.sources.index'));
    }

    /**
     * @param null $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getModalDelete($id = null)
    {
        $error = '';
        $model = '';
        $confirm_route = route('admin.news.sources.delete', ['id' => $id]);
        return View('admin.layouts/modal_confirmation', compact('error', 'model', 'confirm_route'));
    }

    /**
     * @param null $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function getDelete($id = null)
    {
        $sample = NewsSource::destroy($id);

        // Redirect to the group management page
        return redirect(route('admin.news.sources.index'))->with('success', Lang::get('message.success.delete'));
    }

}
