<?php

namespace App\Jobs;

use App\Models\News;
use App\Models\NewsSource;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Feeds;

/**
 * Class FetchNews
 * @package App\Jobs
 */
class FetchNews implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var NewsSource
     */
    protected $source;

    /**
     * FetchNews constructor.
     * @param NewsSource $source
     */
    public function __construct(NewsSource $source)
    {
        $this->source = $source;
    }

    /**
     * @return NewsSource
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $feed = Feeds::make($this->source->url);
        $items = $feed->get_items();
        if ($items) {
            foreach ($items as $item) {
                $hash = md5($this->source->id . $item->get_title() . $item->get_permalink());

                $description = trim($item->get_description());

                $model = News::firstOrNew(['hash' => $hash], [
                    'hash' => $hash,
                    'title' => $item->get_title(),
                    'permalink' => $item->get_permalink(),
                    'description' => $description,
                    'date' => $item->get_date('Y-m-d') ?? date('Y-m-d'),
                    'source_id' => $this->source->id
                ]);

                if (!$model->id) {
                    $model->saveOrFail();

                    if (preg_match('/< *img[^>]*src *= *["\']?([^"\']*)/i', $description, $matches)) {
                        $url = $matches[1];

                        FetchNewsImage::dispatch($model, $url)
                            ->onQueue('default');
                    } else {
                        ParseNewsImage::dispatch($model, $item->get_permalink())
                            ->onQueue('default');
                    }
                }
            }
        }
    }
}
