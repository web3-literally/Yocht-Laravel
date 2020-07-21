<?php

namespace App\Http\Controllers\Admin\Classifieds;

use App\File;
use App\Http\Controllers\Admin\Traits\SeoMetaTrait;
use App\Http\Requests\Classifieds\ClassifiedsRequest;
use App\Models\Classifieds\ClassifiedsCategory;
use App\Models\Classifieds\ClassifiedsImages;
use App\Repositories\Classifieds\ClassifiedsRepository;
use App\Http\Controllers\AppBaseController as InfyOmBaseController;
use Illuminate\Http\Request;
use App\Models\Classifieds\Classifieds;
use Response;
use Flash;
use Yajra\DataTables\DataTables;
use Shop;

/**
 * Class ClassifiedsController
 * @package App\Http\Controllers\Admin\Classifieds
 */
class ClassifiedsController extends InfyOmBaseController
{
    use SeoMetaTrait;

    /**
     * @var ClassifiedsRepository
     */
    private $classifiedsRepository;

    /**
     * ClassifiedsController constructor.
     *
     * @param ClassifiedsRepository $classifiedsRepository
     */
    public function __construct(ClassifiedsRepository $classifiedsRepository)
    {
        $this->classifiedsRepository = $classifiedsRepository;
    }

    /**
     * Display a listing of the Classifieds.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function index(Request $request)
    {
        return view('admin.classifieds.index');
    }

    /**
     * Show the form for editing the specified Classifieds.
     *
     * @param int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $classified = $this->classifiedsRepository->findWithoutFail($id);

        if (empty($classified)) {
            return abort(404);
        }

        $categories = [];
        $list = ClassifiedsCategory::get();
        foreach ($list as $item) {
            if (!isset($categories[$item->type])) {
                $categories[$item->type] = [];
            }
            $categories[$item->type][$item->id] = $item->title;
        }

        $states = Classifieds::getStates();

        if ($classified->location) {
            $locations = [$classified->location->id => "{$classified->location->name} ({$classified->location->country})"];
        } else {
            $locations = [];
        }

        return view('admin.classifieds.edit', compact('categories', 'states', 'locations'))->with('classified', $classified);
    }

    /**
     * Update the specified Classifieds in storage.
     *
     * @param int $id
     * @param ClassifiedsRequest $request
     *
     * @return Response
     *
     * @throws \Exception
     */
    public function update($id, ClassifiedsRequest $request)
    {
        $classified = $this->classifiedsRepository->findWithoutFail($id);

        if (empty($classified)) {
            return abort(404);
        }

        $classified->fill($request->except(['slug', 'meta']));

        if (!$classified->save()) {
            return redirect(route('admin.classifieds.edit', $classified->id))->with('success', 'Failed to save classified.');
        }

        if ($request->hasfile('images')) {
            $storePath = 'classifieds/' . date('Y') . '/' . date('m');
            foreach ($request->file('images') as $i => $file) {
                try {
                    $fl = new File();

                    $fl->mime = $file->getMimeType();
                    $fl->size = $file->getSize();
                    $fl->filename = $file->getClientOriginalName();
                    $fl->disk = 'public';
                    $fl->path = $file->store($storePath, ['disk' => $fl->disk]);
                    $fl->saveOrFail();

                    $classified->attachFile($fl);

                    unset($fl);
                } catch (\Throwable $e) {
                    $request->session()->flash('error', 'Failed to process image.' . $i . ' file.');
                } finally {
                    if (isset($fl->id) && $fl->id) {
                        // Delete file in case if failed to update database
                        $fl->delete();
                    }
                }
            }
        }

        $this->updateSeoData($classified, $request);

        return redirect(route('admin.classifieds.edit', $classified->id))->with('success', 'Classified saved successfully.');
    }

    /**
     * @param $id
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function delete($id, Request $request)
    {
        $classified = Classifieds::find($id);

        if (empty($classified)) {
            return abort(404);
        }

        if (!$classified->delete()) {
            return redirect(route('admin.classifieds.index'))->with('error', 'There was an issue deleting the classified.');
        }

        return redirect(route('admin.classifieds.index'))->with('success', 'Classifieds "' . htmlspecialchars($classified->title) . '" was successfully deleted.');
    }

    /**
     * @param int $id
     * @param int $image
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteImage($id, $image)
    {
        $image = ClassifiedsImages::find($image);

        if (empty($image) || $image->classified_id != $id) {
            return abort(404);
        }

        $success = $image->delete();

        return response()->json(['success' => $success]);
    }

    /**
     * @return mixed
     *
     * @throws \Exception
     */
    public function data()
    {
        $items = Classifieds::get(['id', 'slug', 'user_id', 'title', 'category_id', 'type', 'state', 'price', 'created_at', 'updated_at']);

        return DataTables::of($items)->editColumn('user_id', function (Classifieds $item) {
            return $item->user->full_name;
        })->editColumn('type', function (Classifieds $item) {
            return $item->typeLabel;
        })->editColumn('state', function (Classifieds $item) {
            return $item->stateLabel;
        })->editColumn('category_id', function (Classifieds $item) {
            return $item->category->title;
        })->editColumn('price', function (Classifieds $item) {
            return Shop::format($item->price);
        })->editColumn('updated_at', function (Classifieds $item) {
            return $item->updated_at->diffForHumans();
        })->editColumn('created_at', function (Classifieds $item) {
            return $item->created_at->toFormattedDateString();
        })->addColumn('actions', function (Classifieds $item) {
            $actions = '';
            $actions .= '<a href="' . route('admin.classifieds.edit', $item->id) . '"><i class="livicon" data-name="edit" data-size="18" data-loop="true" data-c="#F89A14" data-hc="#F89A14" title=""></i></a>';
            $actions .= '<a href="' . route('admin.classifieds.delete', $item->id) . '" onclick="return confirm(\'' . 'Are you sure to delete the classified?' . '\');"><i class="livicon" data-name="trash" data-size="18" data-loop="true" data-c="#f56954" data-hc="#f56954" title=""></i></a>';
            return $actions;
        })->rawColumns(['actions'])->make(true);
    }
}
