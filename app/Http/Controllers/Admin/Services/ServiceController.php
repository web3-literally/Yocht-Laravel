<?php

namespace App\Http\Controllers\Admin\Services;

use Intervention\Image\Facades\Image;
use App\Http\Requests\Services\ServiceRequest;
use App\Models\Services\ServiceCategory;
use App\Repositories\ServiceRepository;
use App\Http\Controllers\AppBaseController as InfyOmBaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;
use App\Models\Services\Service;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Cache;

/**
 * Class ServiceController
 * @package App\Http\Controllers\Admin\Services
 */
class ServiceController extends InfyOmBaseController
{
    /**
     * @var  ServiceRepository
     */
    private $serviceRepository;

    /**
     * ServiceController constructor.
     * @param ServiceRepository $serviceRepository
     */
    public function __construct(ServiceRepository $serviceRepository)
    {
        $this->serviceRepository = $serviceRepository;
    }

    public function index(Request $request)
    {
        $this->serviceRepository->pushCriteria(new RequestCriteria($request))
            ->orderBy('category_id', 'asc')
            ->orderBy('parent_id', 'asc')
            ->orderBy('title', 'asc');
        $services = $this->serviceRepository->all();

        return view('admin.services.index')->with('services', $services);
    }

    public function create()
    {
        $categories = ServiceCategory::orderBy('label', 'asc')->pluck('label', 'id')->all();

        /* @var ServiceRepository $serviceRepository */
        $serviceRepository = resolve(ServiceRepository::class);
        $services = $serviceRepository->getDropdownList();

        return view('admin.services.create', compact('categories', 'services'));
    }

    /**
     * @param ServiceRequest $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Throwable
     */
    public function store(ServiceRequest $request)
    {
        $service = new Service($request->except('image'));

        if ($request->hasFile('image')) {
            $imageFileName = $this->processImage($request);
            if ($imageFileName) {
                $service->deleteImage(false);
                $service->image = $imageFileName;
            } else {
                $request->session()->flash('error', 'Failed to process image file.');
            }
        }

        Cache::forget('FindAccountServicesCount');

        if ($service->save()) {
            return redirect(route('admin.services.edit', ['id' => $service->id]))->with('success', 'Service was successfully created.');
        } else {
            return redirect(route('admin.services.edit', ['id' => $service->id]))->withInput()->with('error', 'There was an issue creating the service. Please try again.');
        }
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
     */
    public function edit($id)
    {
        $service = $this->serviceRepository->findWithoutFail($id);

        if (empty($service)) {
            Flash::error('Service not found');

            return redirect(route('services.index'));
        }

        /* @var ServiceRepository $serviceRepository */
        $serviceRepository = resolve(ServiceRepository::class);
        $services = $serviceRepository->getDropdownList([$service->id]);

        $categories = ServiceCategory::orderBy('label', 'asc')->pluck('label', 'id')->all();

        return view('admin.services.edit', compact('categories', 'services'))->with('service', $service);
    }

    /**
     * @param $id
     * @param ServiceRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update($id, ServiceRequest $request)
    {
        $service = Service::findOrFail($id);

        if ($request->hasFile('image')) {
            $imageFileName = $this->processImage($request);
            if ($imageFileName) {
                $service->deleteImage(false);
                $service->image = $imageFileName;
            } else {
                $request->session()->flash('error', 'Failed to process image file.');
            }
        }

        Cache::forget('FindAccountServicesCount');

        if ($service->update($request->except('image'))) {
            return redirect(route('admin.services.edit', ['id' => $service->id]))->with('success', 'Service was successfully updated.');
        } else {
            return redirect(route('admin.services.edit', ['id' => $service->id]))->withInput()->with('error', 'There was an issue updating the service. Please try again.');
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
        $confirm_route = route('admin.services.delete', ['id' => $id]);
        return View('admin.layouts/modal_confirmation', compact('error', 'model', 'confirm_route'));
    }

    /**
     * @param null $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function getDelete($id = null)
    {
        $sample = Service::destroy($id);

        // Redirect to the group management page
        return redirect(route('admin.services.index'))->with('success', Lang::get('message.success.delete'));
    }

    /**
     * @param ServiceRequest $request
     * @return bool|null|string
     */
    protected function processImage(ServiceRequest $request)
    {
        $defaultImgFormat = config('app.default_image_format');

        try {
            if ($request->hasFile('image')) {
                $file = $request->file('image');

                $extension = $file->extension();
                $hash = uniqid();
                $fileName = $hash . '.' . $extension;
                $destinationPath = public_path() . '/uploads/services/';

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
