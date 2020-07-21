<?php

namespace App\Http\Controllers;

use App\Helpers\Owner;
use App\Helpers\PageOffset;
use App\Helpers\RelatedProfile;
use App\Jobs\Index\VesselsAttachmentDelete;
use App\Jobs\Index\VesselsAttachmentUpdate;
use App\Models\Business\Business;
use App\Models\Vessels\Vessel;
use App\Models\Vessels\VesselsAttachment;
use App\User;
use Illuminate\Http\Request;
use Validator;
use App\File;
use Sentinel;
use View;

/**
 * Class VesselsDocumentsController
 * @package App\Http\Controllers
 *
 * TODO: Refactoring needed
 */
class VesselsDocumentsController extends Controller
{
    /**
     * @var string
     */
    protected $globalFolder = 'documents';

    /**
     * @var array
     */
    protected $validationRules = ['required', 'file', 'mimes:pdf,doc,docx,odt,xls,xlsx,ods', 'max:40000'];

    /**
     * @param int $id
     * @return Business|Vessel
     * @throws \Exception
     */
    protected function loadRelated($id)
    {
        $relatedMember = RelatedProfile::currentRelatedMember();
        if (!$relatedMember) {
            abort(404);
        }

        return $relatedMember->profile;
    }

    /**
     * @param int $related_member_id
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

            $storePath = 'vessels/documents/' . date('Y/m');
            try {
                $fl = new File();

                $fl->mime = $file->getMimeType();
                $fl->size = $file->getSize();
                $fl->filename = $file->getClientOriginalName();
                $fl->disk = 'local';
                $fl->path = $file->store($storePath, ['disk' => $fl->disk]);
                $fl->saveOrFail();

                $link = $model->attachFile($fl, 'document', $this->globalFolder);

                unset($fl);

                $result = [
                    'name' => $file->getClientOriginalName(),
                    'size' => $file->getSize()
                ];

                VesselsAttachmentUpdate::dispatch($link->id)
                    ->onQueue('low');
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
     * @param int $related_member_id
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Exception
     */
    public function index($related_member_id, Request $request)
    {
        $model = $this->loadRelated($related_member_id);

        $attachmentTable = File::getModel()->getTable();
        $vesselsAttachmentTable = VesselsAttachment::getModel()->getTable();

        $limit = 10;

        if ($keywords = $request->get('keywords')) {
            $documents = VesselsAttachment::searchByQuery([
                'bool' => [
                    'must' => [
                        'multi_match' => [
                            'query' => $keywords,
                            'fields' => ['file.filename^3', 'attachment.content'],
                            'fuzziness' => 'AUTO'
                        ]
                    ],
                    'filter' => [
                        0 => [
                            'match' => [
                                'user_id' => Sentinel::getUser()->getUserId(),
                            ]
                        ],
                        1 => [
                            'match' => [
                                'vessel_id' => $model->id
                            ]
                        ],
                        2 => [
                            'match' => [
                                'global_folder' => $this->globalFolder
                            ]
                        ]
                    ]
                ],
            ], null, ['id', 'user_id', 'vessel_id', 'file_id', 'type', 'access_mode', 'order'], $limit, PageOffset::offset($limit), ['_score'])->paginate($limit);
        } else {
            $documents = $model->myDocuments()
                ->join($attachmentTable, $vesselsAttachmentTable . '.file_id', '=', $attachmentTable . '.id')
                ->where('vessel_id', $model->id)
                ->where('global_folder', $this->globalFolder)
                ->select($vesselsAttachmentTable . '.*')
                ->orderBy('filename', 'asc')
                ->paginate($limit);
        }

        if ($request->ajax()) {
            $view = View::make('vessels.' . $this->globalFolder . '.index', compact('model', 'documents'));
            $sections = $view->renderSections();
            return $sections['dashboard-content'];
        }

        return view('vessels.' . $this->globalFolder . '.index', compact('model', 'documents'));
    }

    /**
     * @param int $related_member_id
     * @param int $id
     * @return mixed
     * @throws \Exception
     */
    public function details($related_member_id, $id)
    {
        $model = $this->loadRelated($related_member_id);

        $document = $model->myDocuments()->where('global_folder', $this->globalFolder)->findOrFail($id);

        $crew = $model->crew()->where('user_id', '!=', Sentinel::getUser()->getUserId())->get();
        $crewDropdown = $crew->pluck('user.full_name', 'user_id')->all();

        $canRead = $model->documents()->where('file_id', $document->file_id)->where('access_mode', 'read')->get()->pluck('user_id')->all();

        return view('vessels.documents._details', compact('model', 'document', 'crewDropdown', 'canRead'));
    }

    /**
     * @param int $related_member_id
     * @param int $id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function permission($related_member_id, $id, Request $request)
    {
        $model = $this->loadRelated($related_member_id);

        $document = $model->myDocuments(['full'])->where('global_folder', $this->globalFolder)->findOrFail($id);

        $link = $model->crew()->where('user_id', '!=', Sentinel::getUser()->getUserId())->where('user_id', $request->get('member_id'))->first();
        if (!$link) {
            abort(404);
        }

        $grant = $request->get('grant');

        /** @var VesselsAttachment $model */
        $model = VesselsAttachment::firstOrNew([
            'user_id' => $link->user_id,
            'vessel_id' => $model->id,
            'file_id' => $document->file_id,
            'global_folder' => $document->global_folder,
            'folder_id' => $document->folder_id,
            'type' => 'document',
            'access_mode' => 'read',
        ]);

        if ($grant == 'none') {
            $model->delete();
            return response()->json(true);
        }
        if ($grant == 'read') {
            $model->saveOrFail();
            return response()->json(true);
        }

        abort(404);
    }

    /**
     * @param int $related_member_id
     * @param int $id
     * @return bool
     * @throws \Exception
     */
    public function view($related_member_id, $id)
    {
        $model = $this->loadRelated($related_member_id);

        $document = $model->myDocuments()->where('global_folder', $this->globalFolder)->findOrFail($id);

        if ($document->file->mime == 'application/pdf') {
            return response()->file($document->file->getFilePath());
        }
        abort(404);
    }

    /**
     * @param int $related_member_id
     * @param int $id
     * @return bool
     * @throws \Exception
     */
    public function download($related_member_id, $id)
    {
        $model = $this->loadRelated($related_member_id);

        $document = $model->myDocuments()->where('global_folder', $this->globalFolder)->findOrFail($id);

        return response()->download($document->file->getFilePath(), $document->file->filename);
    }

    /**
     * @param int $related_member_id
     * @param int $id
     * @return bool
     * @throws \Exception
     */
    public function remove($related_member_id, $id)
    {
        $model = $this->loadRelated($related_member_id);

        /** @var VesselsAttachment $document */
        $document = $model->myDocuments()->where('global_folder', $this->globalFolder)->findOrFail($id);

        if ($document->access_mode == 'full') {
            if ($document->delete()) {
                VesselsAttachmentDelete::dispatch($document->id)
                    ->onQueue('low');

                return '';
            } else {
                abort(500);
            }
        }
        abort(403);
    }
}
