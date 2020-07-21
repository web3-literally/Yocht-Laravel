<?php

namespace App\Repositories;

use App\File;
use App\User;
use Illuminate\Http\UploadedFile;
use InfyOm\Generator\Common\BaseRepository;
use Intervention\Image\Facades\Image;

class UserRepository extends BaseRepository
{
    /**
     * Configure the Model
     **/
    public function model()
    {
        return User::class;
    }

    /**
     * @param User $user
     * @param UploadedFile $file
     * @return string
     * @throws \Throwable
     */
    public function attachProfileImage(User $user, UploadedFile $file)
    {
        $defaultImgFormat = config('app.default_image_format');

        $extension = $file->extension();
        $hash = uniqid();
        $fileName = $hash . '.' . $extension;
        $destinationPath = public_path() . '/uploads/users/';

        $temp = $file->move($destinationPath, $fileName);

        if ($extension != $defaultImgFormat) {
            $fileName = $hash . '.' . $defaultImgFormat;
            Image::make($temp->getPathname())->encode($defaultImgFormat, 75)->save($destinationPath . $fileName);
            unlink($temp);
        }

        $user->pic = $fileName;
        $user->saveOrFail();

        return $fileName;
    }

    /**
     * @param User $user
     * @param UploadedFile $file
     * @return string
     * @throws \Throwable
     */
    public function attachCV(User $user, UploadedFile $file)
    {
        $storePath = 'vessels/cvs/' . $user->id;
        try {
            if ($user->profile->file_id) {
                $user->profile->file->delete();
            }

            $fl = new File();
            $fl->mime = $file->getMimeType();
            $fl->size = $file->getSize();
            $fl->filename = $file->getClientOriginalName();
            $fl->disk = 'local';
            $fl->path = $file->store($storePath, ['disk' => $fl->disk]);
            $fl->saveOrFail();

            $user->profile->file_id = $fl->id;
            $user->profile->saveOrFail();

            unset($fl);
        } finally {
            if (isset($fl->id) && $fl->id) {
                // Delete file in case if failed to update database
                $fl->delete();
            }
        }

        return true;
    }
}
