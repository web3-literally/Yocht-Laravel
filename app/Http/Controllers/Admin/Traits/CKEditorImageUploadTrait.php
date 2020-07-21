<?php

namespace App\Http\Controllers\Admin\Traits;

use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;

/**
 * Trait CKEditorImageUploadTrait
 * @package App\Http\Controllers\Admin\Traits
 */
trait CKEditorImageUploadTrait
{
    static $imageAllowed = ['png', 'gif', 'jpg', 'jpeg'];

    static $mediaFolder = 'media';

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     *
     * @see http://image.intervention.io/getting_started/configuration
     */
    public function upload(Request $request)
    {
        $defaultImgFormat = 'png';
        $uploaded = 0;
        $error = null;

        try {
            if ($request->hasFile('upload')) {
                $file = $request->file('upload');

                $extension = $file->extension();
                if (!in_array($extension, self::$imageAllowed)) {
                    throw new \Exception('Only ' . implode(', ', self::$imageAllowed) . ' images allowed');
                }

                $hash = md5($file->getClientOriginalName());
                $fileName = $hash . '.' . $extension;
                $destinationPath = public_path() . '/uploads/' . self::$mediaFolder . '/';

                $temp = $file->move($destinationPath, $fileName);

                if ($extension != $defaultImgFormat) {
                    $fileName = $hash . '.' . $defaultImgFormat;
                    Image::make($temp->getPathname())->encode($defaultImgFormat, 75)->save($destinationPath . $fileName);
                    unlink($temp);
                }

                $uploaded++;

                return response()->json([
                    'uploaded' => $uploaded,
                    'fileName' => $fileName,
                    'url' => asset('/uploads/' . self::$mediaFolder . '/' . $fileName)
                ]);
            }
        } catch (\Throwable $e) {
            return response()->json([
                'uploaded' => $uploaded,
                'error' => ['message' => $e->getMessage()]
            ]);
        }

        return response()->json([
            'uploaded' => $uploaded,
            'error' => ['message' => 'Something went wrong']
        ]);
    }
}