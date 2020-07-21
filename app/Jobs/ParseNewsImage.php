<?php

namespace App\Jobs;

use App\Models\News;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

/**
 * Class ParseNewsImage
 * @package App\Jobs
 */
class ParseNewsImage implements ShouldQueue
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
     * ParseNewsImage constructor.
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
        $ch = curl_init($this->url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, '1');
        $text = curl_exec($ch);
        curl_close($ch);

        libxml_use_internal_errors(true);
        $dom = new \DOMDocument;
        $dom->loadHTML($text);
        $xpath = new \DOMXPath($dom);

        $image = null;

        if (stripos($this->model->source->url, 'megayachtnews.com') !== false) {
            $items = $xpath->query("//*[contains(@class, 'thumbnail')]//img");
            if ($items->length) {
                $image = $items->item(0)->getAttribute('src');
            }
        } else {
            // We can parse image url from the page if we know where is it in DOM
            // So, in other cases we skip this parse step
        }

        if ($image) {
            FetchNewsImage::dispatch($this->model, $image)
                ->onQueue('default');
        }
    }
}
