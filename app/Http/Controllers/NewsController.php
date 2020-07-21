<?php

namespace App\Http\Controllers;

use App\Models\News;
use App\Repositories\NewsRepository;
use Illuminate\Support\MessageBag;

/**
 * Class NewsController
 * @package App\Http\Controllers
 */
class NewsController extends Controller
{
    /**
     * @var NewsRepository
     */
    protected $newsRepository;

    /**
     * NewsController constructor.
     * @param MessageBag $messageBag
     * @param NewsRepository $newsRepository
     */
    public function __construct(MessageBag $messageBag, NewsRepository $newsRepository)
    {
        parent::__construct($messageBag);

        $this->newsRepository = $newsRepository;
    }

    /**
     * @return \Illuminate\View\View
     */
    public function index()
    {
        resolve('seotools')->setTitle(trans('general.news'));

        $news = News::orderBy('date', 'desc')->orderBy('id', 'desc')->paginate(10);

        return view('news.index', compact('news'));
    }

    /**
     * @param $year
     * @param $month
     * @param $slug
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show($year, $month, $slug)
    {
        $model = News::where('slug', $slug)->first();
        if (empty($model)) {
            return abort(404);
        }

        if (!is_null($model->source_id)) {
            return redirect($model->permalink);
        }

        resolve('seotools')->setTitle($model->title . config('seotools.meta.defaults.separator') . trans('general.news'));

        return view('news.show', compact('model'));
    }
}
