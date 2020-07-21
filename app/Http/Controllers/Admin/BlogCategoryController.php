<?php

namespace App\Http\Controllers\Admin;

use App\BlogCategory;
use App\Http\Requests\BlogCategoryRequest;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\URL;
use Intervention\Image\Facades\Image;


class BlogCategoryController extends BackEndController
{

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        // Grab all the blog category
        $blogscategories = BlogCategory::orderBy('title', 'asc')->get();
        // Show the page
        return view('admin.blogcategory.index', compact('blogscategories'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        return view('admin.blogcategory.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(BlogCategoryRequest $request)
    {
        $blogcategory = new BlogCategory($request->except('image'));

        if ($request->hasFile('image')) {
            $imageFileName = $this->processImage($request);
            if ($imageFileName) {
                $blogcategory->deleteImage(false);
                $blogcategory->image = $imageFileName;
            } else {
                $request->session()->flash('error', 'Failed to process image file.');
            }
        }

        if ($blogcategory->save()) {
            return redirect('admin/blogcategory')->with('success', trans('blogcategory/message.success.create'));
        } else {
            return Redirect::route('admin/blogcategory')->withInput()->with('error', trans('blogcategory/message.error.create'));
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  BlogCategory $blogCategory
     * @return Response
     */
    public function edit(BlogCategory $blogcategory)
    {
        return view('admin.blogcategory.edit', compact('blogcategory'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  BlogCategory $blogcategory
     * @return Response
     */
    public function update(BlogCategoryRequest $request, BlogCategory $blogcategory)
    {
        if ($request->hasFile('image')) {
            $imageFileName = $this->processImage($request);
            if ($imageFileName) {
                $blogcategory->deleteImage(false);
                $blogcategory->image = $imageFileName;
            } else {
                $request->session()->flash('error', 'Failed to process image file.');
            }
        }

        if ($blogcategory->update($request->except('image'))) {
            return redirect('admin/blogcategory')->with('success', trans('blogcategory/message.success.update'));
        } else {
            return Redirect::route('admin/blogcategory')->withInput()->with('error', trans('blogcategory/message.error.update'));
        }
    }

    /**
     * Remove blog.
     *
     * @param BlogCategory $blogCategory
     * @return Response
     */
    public function getModalDelete(BlogCategory $blogCategory)
    {
        $model = 'blogcategory';
        $confirm_route = $error = null;
        try {
            $confirm_route = route('admin.blogcategory.delete', ['id' => $blogCategory->id]);
            return view('admin.layouts.modal_confirmation', compact('error', 'model', 'confirm_route'));
        } catch (GroupNotFoundException $e) {

            $error = trans('blogcategory/message.error.delete', compact('id'));
            return view('admin.layouts.modal_confirmation', compact('error', 'model', 'confirm_route'));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  BlogCategory $blogCategory
     * @return Response
     */
    public function destroy(BlogCategory $blogCategory)
    {
        if ($blogCategory->delete()) {
            return redirect('admin/blogcategory')->with('success', trans('blogcategory/message.success.destroy'));
        } else {
            return Redirect::route('admin/blogcategory')->withInput()->with('error', trans('blogcategory/message.error.delete'));
        }
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    public function data()
    {
        $posts = BlogCategory::get(['id', 'title', 'created_at']);

        return DataTables::of($posts)->addColumn('title_link', function (BlogCategory $category) {
            return '<a href=' . URL::to('admin/blogcategory/' . $category->id . '/edit') . '>' . htmlspecialchars($category->title) . '</a>';
        })->addColumn('count', function (BlogCategory $category) {
            return $category->blog()->count();
        })->editColumn('created_at', function (BlogCategory $category) {
            return $category->created_at->toFormattedDateString();
        })->addColumn('actions', function (BlogCategory $category) {
            $actions = '<a href=' . URL::to('admin/blogcategory/' . $category->id . '/edit') . '><i class="livicon" data-name="edit" data-size="18" data-loop="true" data-c="#428BCA" data-hc="#428BCA" title="' . trans('blogcategory/table.update-category') . '"></i></a>';
            if ($category->blog()->count()) {
                $actions .= '<a href="#" data-toggle="modal" data-target="#blogcategory_exists" data-name="' . $category->title . '" class="blogcategory_exists"><i class="livicon" data-name="warning-alt" data-size="18" data-loop="true" data-c="#f56954" data-hc="#f56954" title="' . trans('blogcategory/table.category-has-posts') . '"></i></a>';
            } else {
                $actions .= '<a href="' . route('admin.blogcategory.confirm-delete', $category->id) . '" data-toggle="modal" data-id="' . $category->id . '" data-target="#delete_confirm"><i class="livicon" data-name="remove-alt" data-size="18" data-loop="true" data-c="#f56954" data-hc="#f56954" title="'. trans('blogcategory/table.delete-category') .'"></i></a>';
            }
            return $actions;
        })->rawColumns(['title_link', 'actions'])->make(true);
    }

    /**
     * @param BlogCategoryRequest $request
     * @return bool|null|string
     */
    protected function processImage(BlogCategoryRequest $request)
    {
        $defaultImgFormat = config('app.default_image_format');

        try {
            if ($request->hasFile('image')) {
                $file = $request->file('image');

                $extension = $file->extension();
                $hash = uniqid();
                $fileName = $hash . '.' . $extension;
                $destinationPath = public_path() . '/uploads/blog-category/';

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
