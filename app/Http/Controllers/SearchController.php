<?php

namespace App\Http\Controllers;

use App\Repositories\BlogRepository;
use App\Repositories\Classifieds\ClassifiedsRepository;
use App\Repositories\EventsRepository;
use App\Repositories\Jobs\JobsRepository;
use Illuminate\Http\Request;
use Illuminate\Support\MessageBag;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Class SearchController
 * @package App\Http\Controllers
 */
class SearchController extends Controller
{
    /**
     * @var BlogRepository
     */
    protected $blogRepository;

    /**
     * @var JobsRepository
     */
    protected $jobsRepository;

    /**
     * @var EventsRepository
     */
    protected $eventsRepository;

    /**
     * @var ClassifiedsRepository
     */
    protected $classifiedsRepository;

    /**
     * SearchController constructor.
     * @param MessageBag $messageBag
     * @param BlogRepository $blogRepository
     * @param JobsRepository $jobsRepository
     * @param EventsRepository $eventsRepository
     * @param ClassifiedsRepository $classifiedsRepository
     */
    public function __construct(MessageBag $messageBag, BlogRepository $blogRepository, JobsRepository $jobsRepository, EventsRepository $eventsRepository, ClassifiedsRepository $classifiedsRepository)
    {
        parent::__construct($messageBag);

        $this->blogRepository = $blogRepository;
        $this->jobsRepository = $jobsRepository;
        $this->eventsRepository = $eventsRepository;
        $this->classifiedsRepository = $classifiedsRepository;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function search(Request $request)
    {
        resolve('seotools')->setTitle(trans('general.search'));

        $q = $request->get('q');
        $request->validate([
            'q' => 'required|min:3'
        ], [
            'q.*' => 'Please enter 3 or more characters'
        ]);

        $tab = $request->get('tab');
        $limit = config('search.max_results');

        $hasResults = false;
        if ($q) {
            // Blog posts
            if (!$tab || $tab == 'posts') {
                $posts = $this->blogRepository->findByKeywords($q, $limit)->paginate($limit);
            } else {
                $posts = new LengthAwarePaginator([], 0, 1);
            }

            // Jobs
            if (!$tab || $tab == 'jobs') {
                $jobs = $this->jobsRepository->findByKeywords($q, $limit, ['id', 'title', 'slug', 'content', 'image', 'location_address', 'vessel_id'])->paginate($limit);
            } else {
                $jobs = new LengthAwarePaginator([], 0, 1);
            }

            // Events
            if (!$tab || $tab == 'events') {
                $events = $this->eventsRepository->findByKeywords($q, $limit, ['id', 'title', 'slug', 'description', 'image', 'address', 'price', 'category_id'])->paginate($limit);
            } else {
                $events = new LengthAwarePaginator([], 0, 1);
            }

            // Classifieds
            if (!$tab || $tab == 'classifieds') {
                $classifieds = $this->classifiedsRepository->findByKeywords($q, $limit)->paginate($limit);
            } else {
                $classifieds = new LengthAwarePaginator([], 0, 1);
            }

            $hasResults = $posts->count() || $jobs->count() || $events->count() || $classifieds->count();
        } else {
            $posts = $jobs = $events = $classifieds = new LengthAwarePaginator([], 0, 1);
        }

        return view('search', compact('hasResults', 'posts', 'jobs', 'events', 'classifieds'))->with('q', $q);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function quickSearch(Request $request)
    {
        $q = $request->get('q');
        $limit = config('search.max_results');

        $results = ['total' => 0];
        if ($q) {
            // Blog posts
            $posts = $this->blogRepository->findByKeywords($q, $limit, ['id', 'slug', 'title']);
            if ($posts->count()) {
                $results['blog_posts'] = $posts->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'url' => route('blog-post', ['slug' => $item->slug]),
                        'title' => $item->title,
                    ];
                });
                $results['total'] += count($results['blog_posts']);
            }

            // Events
            $events = $this->eventsRepository->findByKeywords($q, $limit, ['id', 'slug', 'title']);
            if ($events->count()) {
                $results['events'] = $events->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'url' => route('events.show', ['slug' => $item->slug]),
                        'title' => $item->title,
                    ];
                });
                $results['total'] += count($results['events']);
            }

            // Classifieds
            $classifieds = $this->classifiedsRepository->findByKeywords($q, $limit, ['id', 'category_slug', 'slug', 'title']);
            if ($classifieds->count()) {
                $results['classifieds'] = $classifieds->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'url' => route('classifieds.show', ['category_slug' => $item->getAttributes()['category_slug'], 'slug' => $item->slug]),
                        'title' => $item->title,
                    ];
                });
                $results['total'] += count($results['classifieds']);
            }
        }

        return response()->json($results);
    }
}
