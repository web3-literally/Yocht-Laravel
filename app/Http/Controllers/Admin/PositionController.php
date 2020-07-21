<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\CreatePositionRequest;
use App\Http\Requests\UpdatePositionRequest;
use App\Models\Position;
use App\Repositories\PositionRepository;
use App\Http\Controllers\AppBaseController as InfyOmBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class PositionController extends InfyOmBaseController
{
    /** @var  PositionRepository */
    private $positionRepository;

    public function __construct(PositionRepository $positionRepo)
    {
        $this->positionRepository = $positionRepo;
    }

    /**
     * Display a listing of the Position.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {

        $this->positionRepository->pushCriteria(new RequestCriteria($request));
        $positions = $this->positionRepository->all();
        return view('admin.positions.index')
            ->with('positions', $positions);
    }

    /**
     * Show the form for creating a new Position.
     *
     * @return Response
     */
    public function create()
    {
        return view('admin.positions.create');
    }

    /**
     * Store a newly created Position in storage.
     *
     * @param CreatePositionRequest $request
     *
     * @return Response
     */
    public function store(CreatePositionRequest $request)
    {
        $input = $request->all();

        $position = $this->positionRepository->create($input);

        Flash::success('Position saved successfully.');

        return redirect(route('admin.positions.index'));
    }

    /**
     * Show the form for editing the specified Position.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $position = $this->positionRepository->findWithoutFail($id);

        if (empty($position)) {
            Flash::error('Position not found');

            return redirect(route('positions.index'));
        }

        return view('admin.positions.edit')->with('position', $position);
    }

    /**
     * Update the specified Position in storage.
     *
     * @param int $id
     * @param UpdatePositionRequest $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function update($id, UpdatePositionRequest $request)
    {
        $position = $this->positionRepository->findWithoutFail($id);


        if (empty($position)) {
            Flash::error('Position not found');

            return redirect(route('positions.index'));
        }

        $position = $this->positionRepository->update($request->all(), $id);

        Flash::success('Position updated successfully.');

        return redirect(route('admin.positions.index'));
    }

    /**
     * Remove the specified Position from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function getModalDelete($id = null)
    {
        $error = '';
        $model = '';
        $confirm_route = route('admin.positions.delete', ['id' => $id]);
        return View('admin.layouts/modal_confirmation', compact('error', 'model', 'confirm_route'));

    }

    public function getDelete($id = null)
    {
        Position::destroy($id);

        // Redirect to the group management page
        return redirect(route('admin.positions.index'))->with('success', trans('message.success.delete'));

    }
}
