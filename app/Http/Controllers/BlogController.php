<?php

namespace App\Http\Controllers;

use App\Blog;
use App\BlogCategory;
use App\BlogComment;
use App\Http\Requests\BlogCommentRequest;
use App\Repositories\BlogRepository;
use Illuminate\Support\Carbon;
use Response;
use Cartalyst\Sentinel\Sentinel;
use Illuminate\Support\MessageBag;

class BlogController extends Controller
{
    protected $blogRepository;

    public function __construct(MessageBag $messageBag, BlogRepository $blogRepository)
    {
        parent::__construct($messageBag);

        $this->blogRepository = $blogRepository;
        $this->categories = BlogCategory::pluck('title', 'id')->all();
        $this->tags = Blog::allTags();
    }

    /**
     * @return \Illuminate\View\View
     */
    public function index()
    {
        resolve('seotools')->setTitle(trans('general.blog'));

        $blogs = $this->blogRepository->listing(request('q'), function($query)  {
            if ($day = request('day')) {
                $query->whereDate('publish_on', $day);
            } else if ($month = request('month')) {
                list($year, $month) = explode('-', $month);
                $query->whereYear('publish_on', $year);
                $query->whereMonth('publish_on', $month);
            }
        });

        $categories = $this->categories;
        $tags = $this->tags;

        return view('blog.blog', compact('blogs', 'categories','tags'));
    }

    /**
     * @param string $slug
     * @param string $category
     * @return \Illuminate\View\View
     */
    public function getPost($category, $slug, Sentinel $sentinel)
    {
        $category = BlogCategory::where('slug', $category)->get()->first();
        if (!$category)
            abort(404);

        $blog = Blog::where('slug', $slug)->first();
        if ($blog) {
            if (($blog->status == Blog::STATUS_PUBLISHED && Carbon::now()->greaterThan($blog->publish_on)) || ($sentinel->check() && $sentinel->getUser()->inRole('admin'))) {
                $blog->increment('views');
            } else {
                abort('404');
            }
        } else {
            abort('404');
        }

        $blog->seoable();

        // Show the page
        return view('blog.blogitem', compact('blog'));
    }

    /**
     * @return \Illuminate\View\View
     */
    public function getBlogCategory($slug)
    {
        $category = BlogCategory::where('slug', $slug)->get()->first();
        if (!$category)
            abort(404);

        $blogs = $this->blogRepository->listing(null, function($query) use ($category) {
            $query->where('blog_category_id', $category->id);
        });

        $categories = $this->categories;
        $tags = $this->tags;

        return view('blog.blog', compact('blogs', 'categories', 'tags'))->with('category', $category);
    }

    /**
     * @return \Illuminate\View\View
     */
    public function getBlogTag($slug)
    {
        $blogs = $this->blogRepository->listing(null, function($query) use ($slug) {
            $query->withAnyTags($slug);
        });

        $categories = $this->categories;
        $tags = $this->tags;

        return view('blog.blog', compact('blogs', 'categories', 'tags'))->with('tag', $slug);
    }

    /**
     * @param BlogCommentRequest $request
     * @param Blog $blog
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function storeComment(BlogCommentRequest $request, Blog $blog)
    {
        $blogcooment = new BlogComment($request->all());
        $blogcooment->blog_id = $blog->id;
        $blogcooment->save();
        return redirect('blogitem/' . $blog->slug);
    }
}
