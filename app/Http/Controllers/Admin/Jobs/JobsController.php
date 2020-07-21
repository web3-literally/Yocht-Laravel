<?php

namespace App\Http\Controllers\Admin\Jobs;

use App\Http\Controllers\Admin\Traits\SeoMetaTrait;
use App\Http\Requests\Jobs\JobRequest;
use App\Models\Jobs\Job;
use App\Http\Controllers\AppBaseController as InfyOmBaseController;
use App\Models\Specialization;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Shop;

/**
 * Class JobsController
 * @package App\Http\Controllers\Admin\Jobs
 */
class JobsController extends InfyOmBaseController
{
    use SeoMetaTrait;

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        return view('admin.jobs.index');
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($id)
    {
        $job = Job::find($id);

        if (empty($job)) {
            return abort(404);
        }

        $specializations = Specialization::orderBy('label', 'asc')->pluck('label', 'id')->all();

        return view('admin.jobs.edit', compact('job'))->with('categories', $specializations);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param $id
     * @param JobRequest $request
     * @return Response
     */
    public function update($id, JobRequest $request)
    {
        $job = Job::find($id);

        if (empty($job)) {
            return abort(404);
        }

        $job->fill($request->except(['slug', 'meta']));

        if ($request->hasFile('image')) {
            $imageFileName = $this->processImage($request);
            if ($imageFileName) {
                $job->deleteImage(false);
                $job->image = $imageFileName;
            } else {
                Flash::success('Failed to process image file.');
            }
        }

        if (!$job->save()) {
            return redirect(route('admin.jobs.edit', $job->id))->with('success', 'Failed to save job.');
        }

        $this->updateSeoData($job, $request);

        return redirect(route('admin.jobs.edit', $job->id))->with('success', 'Job saved successfully.');
    }

    /**
     * @param $id
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function delete($id, Request $request)
    {
        $job = Job::find($id);

        if (empty($job)) {
            return abort(404);
        }

        if (!$job->delete()) {
            return redirect(route('admin.jobs.index'))->with('error', 'There was an issue deleting the job.');
        }

        return redirect(route('admin.jobs.index'))->with('success', 'Job "' . htmlspecialchars($job->title) . '" was successfully deleted.');
    }

    /**
     * @param $id
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deleteImage($id, Request $request)
    {
        $job = Job::find($id);

        if (empty($job)) {
            return abort(404);
        }

        $job->deleteImage();

        return redirect(route('admin.jobs.edit', $job->id))->with('success', 'Job image was successfully deleted.');
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    public function data()
    {
        $items = Job::get(['id', 'slug', 'user_id', 'title', 'category_id', 'status', 'starts_at', 'created_at']);

        return DataTables::of($items)->editColumn('user_id', function (Job $item) {
            return $item->user->full_name;
        })->editColumn('status', function (Job $item) {
            return $item->statusLabel;
        })->editColumn('category_id', function (Job $item) {
            return $item->category->label;
        })->editColumn('starts_at', function (Job $item) {
            return $item->starts_at->toFormattedDateString();
        })->editColumn('created_at', function (Job $item) {
            return $item->created_at->toFormattedDateString();
        })->addColumn('actions', function (Job $item) {
            $actions = '<a href="' . route('jobs.show', $item->slug) . '" target="_blank"><i class="livicon" data-name="external-link" data-size="18" data-loop="true" data-c="#428BCA" data-hc="#428BCA" title=""></i></a>';
            $actions .= '<a href="' . route('admin.jobs.edit', $item->id) . '"><i class="livicon" data-name="edit" data-size="18" data-loop="true" data-c="#F89A14" data-hc="#F89A14" title=""></i></a>';
            $actions .= '<a href="' . route('admin.jobs.delete', $item->id) . '" onclick="return confirm(\''.'Are you sure to delete the job?'.'\');"><i class="livicon" data-name="trash" data-size="18" data-loop="true" data-c="#f56954" data-hc="#f56954" title=""></i></a>';
            return $actions;
        })->rawColumns(['actions'])->make(true);
    }

    /**
     * @param JobRequest $request
     * @return bool|null|string
     */
    protected function processImage(JobRequest $request)
    {
        $defaultImgFormat = config('app.default_image_format');

        try {
            if ($request->hasFile('image')) {
                $file = $request->file('image');

                $extension = $file->extension();
                $hash = uniqid();
                $fileName = $hash . '.' . $extension;
                $destinationPath = public_path() . '/uploads/jobs/';

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
