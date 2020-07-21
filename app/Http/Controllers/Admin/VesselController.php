<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests;
use App\Http\Requests\VesselRequest;
use App\Http\Requests\UpdateVesselRequest;
use App\Repositories\VesselRepository;
use App\Http\Controllers\AppBaseController as InfyOmBaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;
use App\Models\Vessels\Vessel;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class VesselController
 * @package App\Http\Controllers\Admin
 */
class VesselController extends InfyOmBaseController
{
    /**
     * @var VesselRepository
     */
    private $vesselRepository;

    public function __construct(VesselRepository $vesselRepo)
    {
        $this->vesselRepository = $vesselRepo;
    }

    public function index(Request $request)
    {
        $this->vesselRepository->pushCriteria(new RequestCriteria($request));
        $vessels = $this->vesselRepository->all();
        return view('admin.vessels.index')
            ->with('vessels', $vessels);
    }

    public function create()
    {
        return view('admin.vessels.create');
    }

    public function store(VesselRequest $request)
    {
        $input = $request->all();

        $vessel = $this->vesselRepository->create($input);

        Flash::success('Vessel saved successfully.');

        return redirect(route('admin.vessels.index'));
    }

    public function edit($id)
    {
        $vessel = $this->vesselRepository->findWithoutFail($id);

        if (empty($vessel)) {
            Flash::error('Vessel not found');

            return redirect(route('vessels.index'));
        }

        return view('admin.vessels.edit')->with('vessel', $vessel);
    }

    public function update($id, UpdateVesselRequest $request)
    {
        $vessel = $this->vesselRepository->findWithoutFail($id);


        if (empty($vessel)) {
            Flash::error('Vessel not found');

            return redirect(route('vessels.index'));
        }

        $vessel = $this->vesselRepository->update($request->all(), $id);

        Flash::success('Vessel updated successfully.');

        return redirect(route('admin.vessels.index'));
    }

    public function getModalDelete($id = null)
    {
        $error = '';
        $model = '';
        $confirm_route = route('admin.vessels.delete', ['id' => $id]);
        return View('admin.layouts/modal_confirmation', compact('error', 'model', 'confirm_route'));

    }

    public function getDelete($id = null)
    {
        $sample = Vessel::destroy($id);

        // Redirect to the group management page
        return redirect(route('admin.vessels.index'))->with('success', Lang::get('message.success.delete'));

    }
}
