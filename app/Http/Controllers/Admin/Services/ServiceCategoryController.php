<?php

namespace App\Http\Controllers\Admin\Services;

use App\Role;
use Intervention\Image\Facades\Image;
use App\Http\Requests\Services\CategoryRequest;
use App\Repositories\ServiceCategoryRepository;
use App\Http\Controllers\AppBaseController as InfyOmBaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;
use App\Models\Services\ServiceCategory;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;

/**
 * Class ServiceCategoryController
 * @package App\Http\Controllers\Admin\Services
 */
class ServiceCategoryController extends InfyOmBaseController
{
    /**
     * @var  ServiceCategoryRepository
     */
    private $serviceCategoryRepository;

    /**
     * ServiceCategoryController constructor.
     * @param ServiceCategoryRepository $serviceCategoryRepository
     */
    public function __construct(ServiceCategoryRepository $serviceCategoryRepository)
    {
        $this->serviceCategoryRepository = $serviceCategoryRepository;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function index(Request $request)
    {
        $this->serviceCategoryRepository->pushCriteria(new RequestCriteria($request));
        $serviceCategories = $this->serviceCategoryRepository->all();

        return view('admin.services.category.index')->with('serviceCategories', $serviceCategories);
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        $providedBy = Role::searchable()->orderBy('name', 'asc')->pluck('name', 'id')->all();

        return view('admin.services.category.create', compact('providedBy'));
    }

    /**
     * @param CategoryRequest $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws \Throwable
     */
    public function store(CategoryRequest $request)
    {
        $category = new ServiceCategory($request->except('image'));

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
            return redirect(route('admin.services.categories.index'))->with('success', 'Service category was successfully created.');
        } else {
            return redirect(route('admin.services.categories.index'))->withInput()->with('error', 'There was an issue creating the service category. Please try again.');
        }
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
     */
    public function edit($id)
    {
        $serviceCategory = $this->serviceCategoryRepository->findWithoutFail($id);

        if (empty($serviceCategory)) {
            Flash::error('Category not found');

            return redirect(route('serviceCategories.index'));
        }

        $providedBy = Role::searchable()->orderBy('name', 'asc')->pluck('name', 'id')->all();

        return view('admin.services.category.edit', compact('providedBy'))->with('serviceCategory', $serviceCategory);
    }

    /**
     * @param $id
     * @param CategoryRequest $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws \Throwable
     */
    public function update($id, CategoryRequest $request)
    {
        $category = ServiceCategory::findOrFail($id);

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
            return redirect(route('admin.services.categories.index'))->with('success', 'Service category was successfully updated.');
        } else {
            return redirect(route('admin.services.categories.index'))->withInput()->with('error', 'There was an issue updating the service category. Please try again.');
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
        $confirm_route = route('admin.services.categories.delete', ['id' => $id]);
        return View('admin.layouts/modal_confirmation', compact('error', 'model', 'confirm_route'));
    }

    /**
     * @param null $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function getDelete($id = null)
    {
        ServiceCategory::destroy($id);

        // Redirect to the group management page
        return redirect(route('admin.services.categories.index'))->with('success', Lang::get('message.success.delete'));
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
                $destinationPath = public_path() . '/uploads/services-category/';

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
