<?php

namespace App\Http\Controllers;

use App\Models\Classifieds\ClassifiedsManufacturer;

/**
 * Class ManufacturersController
 * @package App\Http\Controllers
 */
class ManufacturersController extends Controller
{
    /**
     * @param int $id
     * @param string $status
     * @param string $key
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function boatSetStatus($id, $status, $key)
    {
        if ($key === sha1(implode('-', [config('manufacturers.secret'), $id, $status]))) {
            $manufacturer = ClassifiedsManufacturer::pending()->whereNull('category_id')->where('type', 'boat')->findOrFail($id);

            if ($status == 'approved') {
                $manufacturer->markApproved();

                return view('manufacturers.boat-status-changed')->with('manufacturer', $manufacturer)->with('status', $status);
            } else if ($status == 'declined') {
                \DB::table('vessels')->where('manufacturer_id', $manufacturer->id)->update([
                    'manufacturer_id' => null
                ]);
                $manufacturer->forceDelete();

                // Send email to member

                return view('manufacturers.boat-status-changed')->with('manufacturer', $manufacturer)->with('status', $status);
            }

            return abort(404);
        }

        return abort(404);
    }

    /**
     * @param int $id
     * @param string $status
     * @param string $key
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function setStatus($id, $status, $key)
    {
        if ($key === sha1(implode('-', [config('manufacturers.secret'), $id, $status]))) {
            $manufacturer = ClassifiedsManufacturer::pending()->findOrFail($id);

            if ($status == 'approved') {
                $manufacturer->markApproved();

                return view('manufacturers.status-changed')->with('manufacturer', $manufacturer)->with('status', $status);
            } else if ($status == 'declined') {
                \DB::table('classifieds')->where('manufacturer_id', $manufacturer->id)->update([
                    'manufacturer_id' => null
                ]);
                $manufacturer->links()->each(function($model) {
                    $model->forceDelete();
                });
                $manufacturer->forceDelete();

                // Send email to member

                return view('manufacturers.status-changed')->with('manufacturer', $manufacturer)->with('status', $status);
            }

            return abort(404);
        }

        return abort(404);
    }
}