<?php

namespace App\Http\Controllers\Admin\Traits;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;

/**
 * Trait SeoMetaTrait
 * @package App\Http\Controllers\Admin\Traits
 */
trait SeoMetaTrait
{
    public function updateSeoData(Model $model, Request $request)
    {
        $meta = $request->get('meta');
        $model->seoData->update([
            'meta' => [
                'title' => $meta['title'] ?? null,
                'description' => $meta['description'] ?? '',
                'keywords' => $meta['keywords'] ?? '',
            ]
        ]);
    }
}