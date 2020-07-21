<?php

namespace App\Jobs;

use App\Models\News;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Curl;

/**
 * Class FetchNewsImage
 * @package App\Jobs
 */
class FetchNewsImage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var News
     */
    protected $model;

    /**
     * @var string
     */
    protected $url;

    /**
     * FetchNewsImage constructor.
     * @param News $model
     * @param string $url
     */
    public function __construct(News $model, string $url)
    {
        $this->model = $model;
        $this->url = $url;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $destinationPath = public_path() . '/uploads/news/';
        $hash = uniqid();
        $fileName = $hash . '.' . pathinfo($this->url, PATHINFO_EXTENSION);

        $response = Curl::to($this->url)
            ->returnResponseObject()
            ->download($destinationPath . $fileName);

        if ($response->status != 200) {
            throw new \Exception($response->message, $response->status);
        }

        $this->model->image = $fileName;
        $this->model->saveOrFail();
    }
}
