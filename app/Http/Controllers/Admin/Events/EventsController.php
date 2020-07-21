<?php

namespace App\Http\Controllers\Admin\Events;

use App\Country;
use App\Http\Controllers\Admin\Traits\SeoMetaTrait;
use App\Http\Controllers\AppBaseController as InfyOmBaseController;
use App\Http\Requests\Events\EventRequest;
use App\Models\Events\Event;
use App\Models\Events\EventCategory;
use Illuminate\Http\Request;
use Shop;
use Sentinel;

/**
 * Class EventsController
 * @package App\Http\Controllers\Admin\Events
 */
class EventsController extends InfyOmBaseController
{
    use SeoMetaTrait;

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        return view('admin.events.index');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function form(Request $request)
    {
        $categories = EventCategory::orderBy('label', 'asc')->pluck('label', 'id')->all();
        $types = Event::getTypes();

        $times = [];
        for($i = 0; $i <= 23; $i++) {
            $value = sprintf('%02d:00', $i);
            $times[$value] = $value;
        }
        $countries = Country::orderBy('name', 'asc')->pluck('name', 'id')->all();

        if ($request->get('id')) {
            $event = Event::findOrFail($request->get('id'));

            return view('admin.events.edit', compact('categories', 'types', 'times', 'countries'))->with('event', $event);
        }

        return view('admin.events.create', compact('categories', 'types', 'times', 'countries'))->with('event', new Event());
    }

    /**
     * @param EventRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(EventRequest $request)
    {
        $result = [];
        try {
            $event = new Event();
            $event->fill($request->except('starts_at', 'ends_at', 'slug', 'meta'));
            $event->fill([
                'starts_at' => $request->get('starts_at') . ' ' . $request->get('starts_time') . ':00',
                'ends_at' => $request->get('ends_at') . ' ' . ($request->get('ends_time') ? $request->get('ends_time') : '23:00') . ':00'
            ]);
            $event->user_id = Sentinel::getUser()->getUserId();


            if ($request->hasFile('image')) {
                $imageFileName = $this->processImage($request);
                if ($imageFileName) {
                    $event->deleteImage(false);
                    $event->image = $imageFileName;
                } else {
                    throw new \Exception('Failed to process image file.');
                }
            }

            $event->saveOrFail();
            $this->updateSeoData($event, $request);
        } catch (\Throwable $e) {
            $result['error'] = $e->getMessage();
        }

        $result['success'] = !isset($result['error']);

        return response()->json($result);
    }

    /**
     * @param $id
     * @param EventRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update($id, EventRequest $request)
    {
        $result = [];
        try {
            $event = Event::findOrFail($id);
            $event->fill($request->except('starts_at', 'ends_at', 'slug', 'meta'));
            $event->fill([
                'starts_at' => $request->get('starts_at') . ' ' . $request->get('starts_time') . ':00',
                'ends_at' => $request->get('ends_at') . ' ' . ($request->get('ends_time') ? $request->get('ends_time') : '23:00') . ':00'
            ]);

            if ($request->hasFile('image')) {
                $imageFileName = $this->processImage($request);
                if ($imageFileName) {
                    $event->deleteImage(false);
                    $event->image = $imageFileName;
                } else {
                    throw new \Exception('Failed to process image file.');
                }
            }

            $event->saveOrFail();
            $this->updateSeoData($event, $request);
        } catch (\Throwable $e) {
            $result['error'] = $e->getMessage();
        }

        $result['success'] = !isset($result['error']);

        return response()->json($result);
    }

    /**
     * @param $id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete($id, Request $request)
    {
        $result = ['success' => false];

        $event = Event::find($id);

        if (empty($event)) {
            return response()->json($result);
        }

        if (!$event->delete()) {
            return response()->json($result);
        }

        $result['success'] = true;
        return response()->json($result);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function data(Request $request)
    {
        $from = $request->get('start') . ' 00:00:00';
        $to = $request->get('end') . ' 00:00:00';

        $events = Event::whereBetween('starts_at', [$from, $to])->get();

        $results = [];
        if ($events) {
            foreach($events as $event) {
                $results[] = [
                    'id' => $event->id,
                    'view_url' => route('events.show', $event->slug),
                    'edit_url' => route('admin.events.form', ['id' => $event->id]),
                    'delete_url' => route('admin.events.delete', $event->id),
                    'title' => $event->title,
                    'start' => $event->starts_at->format('Y-m-d H:i:s'),
                    'end' => $event->ends_at->format('Y-m-d H:i:s'),
                    'backgroundColor' => '#515863'
                ];
            }
        }

        return response()->json($results);
    }

    /**
     * @param EventRequest $request
     * @return bool|null|string
     */
    protected function processImage(EventRequest $request)
    {
        $defaultImgFormat = config('app.default_image_format');

        try {
            if ($request->hasFile('image')) {
                $file = $request->file('image');

                $extension = $file->extension();
                $hash = uniqid();
                $fileName = $hash . '.' . $extension;
                $destinationPath = public_path() . '/uploads/events/';

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
