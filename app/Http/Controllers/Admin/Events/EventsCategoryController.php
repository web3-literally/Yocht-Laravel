<?php

namespace App\Http\Controllers\Admin\Events;

use App\Http\Controllers\Admin\BackEndController;
use App\Http\Requests\Events\CategoryRequest;
use App\Models\Events\EventCategory;
use Intervention\Image\Facades\Image;

/**
 * Class EventsCategoryController
 * @package App\Http\Controllers\Admin
 */
class EventsCategoryController extends BackEndController
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $categories = EventCategory::orderBy('label', 'asc')->get();

        return view('admin.events.category.index', compact('categories'));
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        return view('admin.events.category.create');
    }

    /**
     * @param CategoryRequest $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Throwable
     */
    public function store(CategoryRequest $request)
    {
        $category = new EventCategory($request->except('image'));

        if ($request->hasFile('image')) {
            $imageFileName = $this->processImage($request);
            if ($imageFileName) {
                $category->deleteImage(false);
                $category->image = $imageFileName;
            } else {
                $request->session()->flash('error', 'Failed to process image file.');
            }
        }

        if ($category->save()) {
            return redirect('admin/events/categories')->with('success', 'Event category was successfully created.');
        } else {
            return Redirect::route('admin/events/categories')->withInput()->with('error', 'There was an issue creating the event category. Please try again.');
        }
    }

    /**
     * @param int $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($id)
    {
        $category = EventCategory::findOrFail($id);

        return view('admin.events.category.edit', compact('category'));
    }

    /**
     * @param int $id
     * @param CategoryRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update($id, CategoryRequest $request)
    {
        $category = EventCategory::findOrFail($id);

        if ($request->hasFile('image')) {
            $imageFileName = $this->processImage($request);
            if ($imageFileName) {
                $category->deleteImage(false);
                $category->image = $imageFileName;
            } else {
                $request->session()->flash('error', 'Failed to process image file.');
            }
        }

        if ($category->update($request->except('image'))) {
            return redirect('admin/events/categories')->with('success', 'Event category was successfully updated.');
        } else {
            return Redirect::route('admin/events/categories')->withInput()->with('error', 'There was an issue updating the event category. Please try again.');
        }
    }

    /**
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $category = EventCategory::findOrFail($id);

        if ($category->events->count()) {
            return abort(404);
        }

        if ($category->delete()) {
            $category->deleteImage(false);
            return redirect('admin/events/categories')->with('success', 'Event category was successfully deleted.');
        } else {
            return Redirect::route('admin/events/categories')->withInput()->with('error', 'There was an issue deleting the event category. Please try again.');
        }
    }

    /**
     * @param CategoryRequest $request
     * @return bool|null|string
     */
    protected function processImage(CategoryRequest $request)
    {
        $defaultImgFormat = config('app.default_image_format');

        try {
            if ($request->hasFile('image')) {
                $file = $request->file('image');

                $extension = $file->extension();
                $hash = uniqid();
                $fileName = $hash . '.' . $extension;
                $destinationPath = public_path() . '/uploads/events-category/';

                $temp = $file->move($destinationPath, $fileName);

                if ($extension != $defaultImgFormat) {
                    $fileName = $hash . '.' . $defaultImgFormat;
                    Image::make($temp->getPathname())->encode($defaultImgFormat, 75)->save($destinationPath . $fileName);
                    unlink($temp);
                }

                return $fileName;
            }
        } catch (\Throwable $e) {
            report($e);

            return false;
        }

        return null;
    }
}
