<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\VesselManufacturerRequest;
use App\Repositories\VesselManufacturerRepository;
use App\Http\Controllers\AppBaseController as InfyOmBaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;
use App\Models\Vessels\VesselManufacturer;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class VesselManufacturerController extends InfyOmBaseController
{
    /**
     * @var  VesselManufacturerRepository
     */
    private $vesselManufacturerRepository;

    /**
     * VesselManufacturerController constructor.
     * @param VesselManufacturerRepository $vesselManufacturerRepository
     */
    public function __construct(VesselManufacturerRepository $vesselManufacturerRepository)
    {
        $this->vesselManufacturerRepository = $vesselManufacturerRepository;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function index(Request $request)
    {

        $this->vesselManufacturerRepository->pushCriteria(new RequestCriteria($request));
        $vesselManufacturers = $this->vesselManufacturerRepository->all();
        return view('admin.vessels.manufacturers.index')
            ->with('vesselManufacturers', $vesselManufacturers);
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        return view('admin.vessels.manufacturers.create');
    }

    /**
     * @param VesselManufacturerRequest $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function store(VesselManufacturerRequest $request)
    {
        $input = $request->all();

        $vesselManufacturer = $this->vesselManufacturerRepository->create($input);

        Flash::success('VesselManufacturer saved successfully.');

        return redirect(route('admin.vessels.manufacturers.index'));
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
     */
    public function edit($id)
    {
        $vesselManufacturer = $this->vesselManufacturerRepository->findWithoutFail($id);

        if (empty($vesselManufacturer)) {
            Flash::error('VesselManufacturer not found');

            return redirect(route('vessels.manufacturers.index'));
        }

        return view('admin.vessels.manufacturers.edit')->with('vesselManufacturer', $vesselManufacturer);
    }

    /**
     * @param $id
     * @param VesselManufacturerRequest $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function update($id, VesselManufacturerRequest $request)
    {
        $vesselManufacturer = $this->vesselManufacturerRepository->findWithoutFail($id);


        if (empty($vesselManufacturer)) {
            Flash::error('VesselManufacturer not found');

            return redirect(route('vessels.manufacturers.index'));
        }

        $vesselManufacturer = $this->vesselManufacturerRepository->update($request->all(), $id);

        Flash::success('VesselManufacturer updated successfully.');

        return redirect(route('admin.vessels.manufacturers.index'));
    }

    /**
     * @param null $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getModalDelete($id = null)
    {
        $error = '';
        $model = '';
        $confirm_route = route('admin.vessels.manufacturers.delete', ['id' => $id]);
        return View('admin.layouts/modal_confirmation', compact('error', 'model', 'confirm_route'));

    }

    /**
     * @param null $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function getDelete($id = null)
    {
        VesselManufacturer::destroy($id);

        // Redirect to the group management page
        return redirect(route('admin.vessels.manufacturers.index'))->with('success', Lang::get('message.success.delete'));

    }

}
