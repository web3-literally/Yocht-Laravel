<?php

namespace App\Http\Controllers;

use App\Jobs\Index\VesselsAttachmentDelete;
use App\Jobs\Index\VesselsAttachmentUpdate;
use App\Models\Vessels\VesselsAttachment;
use Illuminate\Http\Request;
use Validator;
use App\File;
use View;

/**
 * Class VesselsPublicAttachmentsController
 * @package App\Http\Controllers
 *
 * TODO: Refactoring needed
 */
class VesselsPublicAttachmentsController extends VesselsDocumentsController
{
    /**
     * @var string
     */
    protected $globalFolder = 'public';

    /**
     * @var array
     */
    protected $validationRules = ['required', 'file', 'mimes:pdf', 'max:40000'];

    /**
     * @param $related_member_id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function upload($related_member_id, Request $request)
    {
        $model = $this->loadRelated($related_member_id);

        if ($request->hasfile('file')) {
            $file = $request->file('file');

            $validator = Validator::make($request->all(), [
                'file' => $this->validationRules
            ]);

            if ($validator->fails()) {
                $bag = $validator->getMessageBag();
                $message = $bag->first('file');
                $result = [
                    'name' => $file->getClientOriginalName(),
                    'size' => $file->getSize(),
                    'error' => $message
                ];

                return response()->json(['file' => $result]);
            }

            $storePath = 'vessels/attachments/' . date('Y/m');
            try {
                $fl = new File();

                $fl->mime = $file->getMimeType();
                $fl->size = $file->getSize();
                $fl->filename = $file->getClientOriginalName();
                $fl->disk = 'public';
                $fl->path = $file->store($storePath, ['disk' => $fl->disk]);
                $fl->saveOrFail();

                $link = $model->attachFile($fl, 'document', $this->globalFolder);

                unset($fl);

                $result = [
                    'name' => $file->getClientOriginalName(),
                    'size' => $file->getSize()
                ];

                /*VesselsAttachmentUpdate::dispatch($link->id)
                    ->onQueue('low');*/
            } catch (\Throwable $e) {
                $result = [
                    'name' => $file->getClientOriginalName(),
                    'size' => $file->getSize(),
                    'error' => $e->getMessage()
                ];

                report($e);
            } finally {
                if (isset($fl->id) && $fl->id) {
                    // Delete file in case if failed to update database
                    $fl->delete();
                }
            }
        }

        return response()->json(['file' => $result]);
    }

    /**
     * @param $related_member_id
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Exception
     */
    public function index($related_member_id, Request $request)
    {
        $boat = $this->loadRelated($related_member_id);

        $attachmentTable = File::getModel()->getTable();
        $vesselsAttachmentTable = VesselsAttachment::getModel()->getTable();

        $limit = 10;

        $documents = $boat->myDocuments()
            ->join($attachmentTable, $vesselsAttachmentTable . '.file_id', '=', $attachmentTable . '.id')
            ->where('vessel_id', $boat->id)
            ->where('global_folder', $this->globalFolder)
            ->select($vesselsAttachmentTable . '.*')
            ->orderBy('filename', 'asc')
            ->paginate($limit);

        if ($request->ajax()) {
            $view = View::make('vessels.attachments.index', compact('boat', 'documents'))->with('vessel', $boat);
            $sections = $view->renderSections();
            return $sections['dashboard-content'];
        }

        return view('vessels.attachments.index', compact('boat', 'documents'))->with('vessel', $boat);
    }

    /**
     * @param int $related_member_id
     * @param int $boat_id
     * @param int $id
     * @return mixed
     * @throws \Exception
     */
    public function publicDetails($related_member_id, $boat_id, $id)
    {
        $boat = $this->loadRelated($related_member_id);

        $document = $boat->myDocuments()->where('global_folder', $this->globalFolder)->findOrFail($id);

        return view('vessels.attachments._details', compact('boat', 'document'));
    }

    /**
     * @param int $related_member_id
     * @param int $boat_id
     * @param int $id
     * @return bool
     * @throws \Exception
     */
    public function publicRemove($related_member_id, $boat_id, $id)
    {
        $boat = $this->loadRelated($related_member_id);

        /** @var VesselsAttachment $document */
        $document = $boat->myDocuments()->where('global_folder', $this->globalFolder)->findOrFail($id);

        if ($document->access_mode == 'full') {
            if ($document->delete()) {
                /*VesselsAttachmentDelete::dispatch($document->id)
                    ->onQueue('low');*/

                return '';
            } else {
                abort(500);
            }
        }
        abort(403);
    }
}
