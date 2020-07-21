<?php

namespace App\Http\Controllers\Admin;

use App\File;
use App\Helpers\Posts;
use App\Blog;
use App\BlogCategory;
use App\BlogComment;
use App\Http\Controllers\Admin\Traits\SeoMetaTrait;
use App\Http\Requests\BlogCommentRequest;
use App\Http\Requests\BlogRequest;
use Response;
use Sentinel;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\URL;
use App\Http\Controllers\Admin\Traits\CKEditorImageUploadTrait;
use Illuminate\Support\MessageBag;

/**
 * Class BlogController
 * @package App\Http\Controllers\Admin
 */
class BlogController extends BackEndController
{
    use CKEditorImageUploadTrait;
    use SeoMetaTrait;

    private $tags;

    public function __construct(MessageBag $messageBag)
    {
        parent::__construct($messageBag);
        $this->tags = Blog::allTags();
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        // Grab all the blogs
        //$blogs = Blog::orderBy('id', 'desc')->take(10)->get();
        $blogs = [];
        $statuses = Posts::getPostStatuses();
        // Show the page
        return view('admin.blog.index', compact('blogs', 'statuses'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $blogcategory = BlogCategory::pluck('title', 'id');
        $statuses = Posts::getPostStatuses();

        return view('admin.blog.create', compact('blogcategory', 'statuses'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(BlogRequest $request)
    {
        $blog = new Blog($request->except('image', 'video', 'files', '_method', 'tags', 'close', 'meta'));

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $extension = $file->extension() ?: 'png';
            $picture = str_random(10) . '.' . $extension;
            $destinationPath = public_path() . '/uploads/blog/';
            $file->move($destinationPath, $picture);
            $blog->image = $picture;
        }

        $oldFileId = null;
        if ($request->hasFile('video')) {
            $oldFileId = $blog->video_id;

            $fl = new File();
            $file = $request->file('video');

            $fl->mime = $file->getMimeType();
            $fl->size = $file->getSize();
            $fl->filename = $file->getClientOriginalName();
            $fl->disk = 'public';
            $fl->path = $file->store('attachments', ['disk' => $fl->disk]);
            $fl->saveOrFail();

            $blog->video_id = $fl->id;
        }

        $blog->user_id = Sentinel::getUser()->id;
        $blog->save();

        if ($oldFileId) {
            $fl = File::find($oldFileId);
            $fl->delete();
        }

        $blog->tag($request->tags ? $request->tags : '');

        $this->updateSeoData($blog, $request);

        if ($blog->id) {
            return redirect('admin/blog')->with('success', trans('blog/message.success.create'));
        } else {
            return Redirect::route('admin/blog')->withInput()->with('error', trans('blog/message.error.create'));
        }
    }


    /**
     * Display the specified resource.
     *
     * @param  Blog $blog
     * @return view
     */
    public function show(Blog $blog)
    {
        $comments = Blog::find($blog->id)->comments;
        $statuses = Posts::getPostStatuses();

        return view('admin.blog.show', compact('blog', 'comments', 'tags', 'statuses'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Blog $blog
     * @return view
     */
    public function edit(Blog $blog)
    {
        $blogcategory = BlogCategory::pluck('title', 'id');
        $statuses = Posts::getPostStatuses();

        return view('admin.blog.edit', compact('blog', 'blogcategory', 'statuses'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Blog $blog
     * @return Response
     */
    public function update(BlogRequest $request, Blog $blog)
    {
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $extension = $file->extension() ?: 'png';
            $picture = str_random(10) . '.' . $extension;
            $destinationPath = public_path() . '/uploads/blog';
            $file->move($destinationPath, $picture);
            $blog->image = $picture;
        }

        $oldFileId = null;
        if ($request->hasFile('video')) {
            $oldFileId = $blog->video_id;

            $fl = new File();
            $file = $request->file('video');

            $fl->mime = $file->getMimeType();
            $fl->size = $file->getSize();
            $fl->filename = $file->getClientOriginalName();
            $fl->disk = 'public';
            $fl->path = $file->store('attachments', ['disk' => $fl->disk]);
            $fl->saveOrFail();

            $blog->video_id = $fl->id;
        }

        $blog->retag($request->tags ? $request->tags : '');

        if ($blog->update($request->except('image', 'video', 'files', '_method', 'tags', 'close', 'meta'))) {
            $this->updateSeoData($blog, $request);
            $redirect = redirect();
            $redirect = $request->exists('close') ? $redirect->to('admin/blog') : $redirect->back();

            if ($oldFileId) {
                $fl = File::find($oldFileId);
                $fl->delete();
            }
            return $redirect->with('success', trans('blog/message.success.update'));
        } else {
            return redirect()->back()->withInput()->with('error', trans('blog/message.error.update'));
        }
    }

    /**
     * Remove blog.
     *
     * @param Blog $blog
     * @return Response
     */
    public function getModalDelete(Blog $blog)
    {
        $model = 'blog';
        $confirm_route = $error = null;
        try {
            $confirm_route = route('admin.blog.delete', ['id' => $blog->id]);
            return view('admin.layouts.modal_confirmation', compact('error', 'model', 'confirm_route'));
        } catch (GroupNotFoundException $e) {

            $error = trans('blog/message.error.destroy', compact('id'));
            return view('admin.layouts.modal_confirmation', compact('error', 'model', 'confirm_route'));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Blog $blog
     * @return Response
     */
    public function destroy(Blog $blog)
    {
        if ($blog->delete()) {
            return redirect('admin/blog')->with('success', trans('blog/message.success.delete'));
        } else {
            return Redirect::route('admin/blog')->withInput()->with('error', trans('blog/message.error.delete'));
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param BlogCommentRequest $request
     * @param Blog $blog
     *
     * @return Response
     */
    public function storeComment(BlogCommentRequest $request, Blog $blog)
    {
        $blogcooment = new BlogComment($request->all());
        $blogcooment->blog_id = $blog->id;
        $blogcooment->save();
        return redirect('admin/blog/' . $blog->id );
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    public function data()
    {
        $posts = Blog::get(['id', 'title', 'slug', 'status', 'views', 'publish_on', 'updated_at']);
        $statuses = Posts::getPostStatuses();

        return DataTables::of($posts)->addColumn('title_link', function (Blog $blog) {
            return '<a href=' . URL::to('admin/blog/' . $blog->id) . '>' . htmlspecialchars($blog->title) . '</a>';
        })->addColumn('comments', function (Blog $blog) {
            return $blog->comments()->count();
        })->editColumn('updated_at', function (Blog $blog) {
            return $blog->updated_at->diffForHumans();
        })->editColumn('status', function (Blog $blog) use ($statuses) {
            return $statuses[$blog->status];
        })->editColumn('publish_on', function (Blog $blog) {
            return $blog->publish_on->toDayDateTimeString();
        })->addColumn('actions', function (Blog $blog) {
            $actions = '<a href=' . URL::to('admin/blog/' . $blog->id) . '><i class="livicon" data-name="info" data-size="18" data-loop="true" data-c="#428BCA" data-hc="#428BCA" title="' . trans('blog/table.view-blog-comment') . '"></i></a>';
            $actions .= '<a href=' . URL::to('admin/blog/' . $blog->id . '/edit') . '><i class="livicon" data-name="edit" data-size="18" data-loop="true" data-c="#428BCA" data-hc="#428BCA" title="' . trans('blog/table.update-blog') . '"></i></a>';
            $actions .= '<a href=' . route('admin.blog.confirm-delete', $blog->id) . ' data-toggle="modal" data-id="' . $blog->id . '" data-target="#delete_confirm"><i class="livicon" data-name="trash" data-size="18" data-loop="true" data-c="#f56954" data-hc="#f56954" title="' . trans('blog/table.delete-blog') . '"></i></a>';
            return $actions;
        })->rawColumns(['title_link', 'actions'])->make(true);
    }
}
