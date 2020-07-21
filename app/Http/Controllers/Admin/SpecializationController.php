<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\CreateSpecializationRequest;
use App\Http\Requests\UpdateSpecializationRequest;
use App\Models\Position;
use App\Repositories\SpecializationRepository;
use App\Http\Controllers\AppBaseController as InfyOmBaseController;
use Illuminate\Http\Request;
use App\Models\Specialization;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class SpecializationController extends InfyOmBaseController
{
    /** @var  SpecializationRepository */
    private $specializationRepository;

    protected $positions = [];

    public function __construct(SpecializationRepository $specializationRepository)
    {
        $this->specializationRepository = $specializationRepository;

        $positions = Position::orderBy('label', 'asc')->pluck('label', 'id')->all();

        $this->positions = $positions;
    }

    /**
     * Display a listing of the Specialization.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {

        $this->specializationRepository->pushCriteria(new RequestCriteria($request));
        $specializations = $this->specializationRepository->all();
        return view('admin.specializations.index')
            ->with('specializations', $specializations);
    }

    /**
     * Show the form for creating a new Specialization.
     *
     * @return Response
     */
    public function create()
    {
        return view('admin.specializations.create', ['positions' => $this->positions]);
    }

    /**
     * Store a newly created Specialization in storage.
     *
     * @param CreateSpecializationRequest $request
     *
     * @return Response
     */
    public function store(CreateSpecializationRequest $request)
    {
        $input = $request->all();

        $specialization = $this->specializationRepository->create($input);

        Flash::success('Specialization saved successfully.');

        return redirect(route('admin.specializations.index'));
    }

    /**
     * Show the form for editing the specified Specialization.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $specialization = $this->specializationRepository->findWithoutFail($id);

        if (empty($specialization)) {
            Flash::error('Specialization not found');

            return redirect(route('specializations.index'));
        }

        return view('admin.specializations.edit', ['positions' => $this->positions])->with('specialization', $specialization);
    }

    /**
     * Update the specified Specialization in storage.
     *
     * @param  int $id
     * @param UpdateSpecializationRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateSpecializationRequest $request)
    {
        $specialization = $this->specializationRepository->findWithoutFail($id);


        if (empty($specialization)) {
            Flash::error('Specialization not found');

            return redirect(route('specializations.index'));
        }

        $specialization = $this->specializationRepository->update($request->all(), $id);

        Flash::success('Specialization updated successfully.');

        return redirect(route('admin.specializations.index'));
    }

    /**
     * Remove the specified Specialization from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function getModalDelete($id = null)
    {
        $error = '';
        $model = '';
        $confirm_route = route('admin.specializations.delete', ['id' => $id]);
        return View('admin.layouts/modal_confirmation', compact('error', 'model', 'confirm_route'));

    }

    public function getDelete($id = null)
    {
        $sample = Specialization::destroy($id);

        // Redirect to the group management page
        return redirect(route('admin.specializations.index'))->with('success', trans('message.success.delete'));

    }

}
