<?php

namespace App\Http\Controllers\V1\Attachment;

use App\Http\Controllers\Controller;
use App\Services\V1\Attachment\AttachmentService;
use Illuminate\Http\Request;

class AttachmentController extends Controller
{
    public function __construct(public AttachmentService $attachmentService) {}

    public function uploadFile(Request $request) {
        $response = $this->attachmentService->uploadFile($request->file('file')->getClientOriginalName(), 'workspace_files');
        return $this->respondWithCustomData('File uploaded successfully.', $response, 201);
    }
}
