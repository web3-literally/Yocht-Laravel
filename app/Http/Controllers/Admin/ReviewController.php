<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\UpdateReviewRequest;
use App\Jobs\Index\ReviewsUpdate;
use App\Repositories\ReviewRepository;
use App\Http\Controllers\AppBaseController as InfyOmBaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;
use App\Models\Reviews\Review;
use Flash;
use Response;
use Yajra\DataTables\DataTables;
use DB;

/**
 * Class ReviewController
 * @package App\Http\Controllers\Admin
 */
class ReviewController extends InfyOmBaseController
{
    /**
     * @var  ReviewRepository
     */
    private $reviewRepository;

    public function __construct(ReviewRepository $reviewRepo)
    {
        $this->reviewRepository = $reviewRepo;
    }

    public function index(Request $request)
    {
        $reviews = [];

        return view('admin.reviews.index')
            ->with('reviews', $reviews);
    }

    public function edit($id)
    {
        $review = $this->reviewRepository->findWithoutFail($id);

        if (empty($review)) {
            Flash::error('Review not found');

            return redirect(route('reviews.index'));
        }

        return view('admin.reviews.edit')->with('review', $review);
    }

    public function update($id, UpdateReviewRequest $request)
    {
        $review = $this->reviewRepository->findWithoutFail($id);

        if (empty($review)) {
            Flash::error('Review not found');

            return redirect(route('reviews.index'));
        }

        $review = $this->reviewRepository->update($request->only(['title', 'message']), $id);

        Flash::success('Review updated successfully.');

        return redirect(route('admin.reviews.index'));
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    public function data()
    {
        $items = Review::get(['id', 'title', 'message', 'status', 'rating', 'by_id', 'recommendation', 'created_at']);

        return DataTables::of($items)->addColumn('multi_select', function (Review $item) {
            return '<input  class="multi-select-input" type="checkbox" name="item[' . $item->id . ']" value="1">';
        })->addColumn('review', function (Review $item) {
            return '<label>' . $item->title . '</label><p>' . $item->message . '</p>';
        })->addColumn('status', function (Review $item) {
            return $item->statusLabel;
        })->editColumn('by_id', function (Review $item) {
            return $item->by->full_name;
        })->editColumn('recommendation', function (Review $item) {
            if (is_null($item->recommendation)) {
                return '';
            }
            return $item->recommendation ? trans('general.yes') : trans('general.no');
        })->editColumn('created_at', function (Review $item) {
            return $item->created_at->toFormattedDateString();
        })->addColumn('actions', function (Review $item) {
            $actions = '';
            $actions .= '<a href="' . route('admin.reviews.edit', $item->id) . '"><i class="livicon" data-name="edit" data-size="18" data-loop="true" data-c="#F89A14" data-hc="#F89A14" title=""></i></a>';
            return $actions;
        })->rawColumns(['multi_select', 'review', 'actions'])->make(true);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function setStatus(Request $request)
    {
        $status = request('status');
        $items = $request->get('item');

        $result = 0;

        if ($items && in_array($status, ['approved', 'declined'])) {
            $items = array_keys($items);

            $reviewTable = (new Review())->getTable();
            $result = DB::table($reviewTable)->whereIn('id', $items)->update(['status' => $status]);

            Review::whereIn('id', $items)->with('for')->get()->map(function($review) {
                ReviewsUpdate::dispatchNow($review);
            });
        }

        return response()->json([
            'success' => $result,
            'items' => $items
        ]);
    }
}
