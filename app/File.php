<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use File as FileSystem;

/**
 * Class File
 * @package App
 */
class File extends Model
{
    protected $table = 'files';

    protected $guarded = ['id'];

    public $fillable = [
        'name'
    ];

    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * @param int $size
     * @param int $precision
     * @return string
     */
    public static function formatBytes($size, $precision = 2)
    {
        if ($size > 0) {
            $size = (int)$size;
            $base = log($size) / log(1024);
            $suffixes = array(' bytes', ' KB', ' MB', ' GB', ' TB');

            return round(pow(1024, $base - floor($base)), $precision) . $suffixes[floor($base)];
        } else {
            return (string)$size;
        }
    }

    /**
     * @return string
     */
    public function getSizeTitleAttribute()
    {
        return self::formatBytes($this->size);
    }

    /**
     * @return mixed
     */
    public function getFilePath()
    {
        return Storage::disk($this->disk)->path($this->path);
    }

    /**
     * @return mixed
     */
    public function getFileUrl()
    {
        return Storage::disk($this->disk)->url($this->path);
    }

    /**
     * @return null|mixed
     */
    public function getPublicUrl()
    {
        if ($this->disk == 'public') {
            return $this->getFileUrl();
        }

        return null;
    }

    /**
     * @return bool
     */
    public function isImage()
    {
        return (stripos($this->mime, 'image') === 0 && $this->disk == 'public');
    }

    /**
     * @return string
     */
    public function getOriginalImage()
    {
        return Storage::disk($this->disk)->url($this->path);
    }

    /**
     * @param string $size
     * @return string
     */
    public function getThumb($size)
    {
        $placeholderUrl = str_replace('{size}', $size, config('app.placeholder_url'));
        if (!$this->isImage()) {
            return $placeholderUrl;
        }

        $thumbUrl = $this->genThumb($size);
        if (!$thumbUrl) {
            return $placeholderUrl;
        }
        return Storage::disk($this->disk)->url($thumbUrl);
    }

    /**
     * @param string $size
     * @return bool|string
     */
    protected function genThumb($size)
    {
        $sourceImage = $this->path;
        $sourceFolder = dirname($sourceImage) . '/';
        $sourceFileName = basename($sourceImage);

        $size = strtolower($size);
        try {
            if (!preg_match('/^([0-9]{0,4})x([0-9]{0,4})$/', $size)) {
                throw new \Exception('Wrong thumbnail image size');
            }
            $sourcePath = Storage::disk($this->disk)->path($sourceImage);
            $path = Storage::disk($this->disk)->path($sourceFolder . $size) . '/';
            $filePath = $path . $sourceFileName;
            if (!file_exists($filePath)) {
                if (!is_dir($path)) {
                    mkdir($path, 0777, true);
                }
                list($width, $height) = explode('x', $size);
                Image::make($sourcePath)->fit($width, $height)->save($filePath);
            }
            return $sourceFolder . $size . '/' . $sourceFileName;
        } catch (\Throwable $e) {
            return false;
        }

        return $sourceFolder . $sourceFileName;
    }

    /**
     * @param bool $cleanup
     * @return bool|null
     * @throws \Exception
     */
    public function delete($cleanup = true)
    {
        if (parent::delete()) {
            if ($cleanup)
                $this->cleanup();

            return true;
        }

        return false;
    }

    public function cleanup()
    {
        $path = $this->path;
        $result = Storage::disk($this->disk)->delete($path);

        if ($result && $this->isImage()) {
            $destinationFileName = basename($path);
            $destinationPath = Storage::disk($this->disk)->path(dirname($path)) . '/';

            $folders = FileSystem::glob($destinationPath . '*', GLOB_ONLYDIR);
            if ($folders) {
                foreach ($folders as $folder) {
                    $filePath = $destinationPath . basename($folder) . '/' . $destinationFileName;
                    if (FileSystem::exists($filePath)) {
                        $result = $result && FileSystem::delete($filePath);
                    }
                }
            }
        }

        return $result;
    }
}
