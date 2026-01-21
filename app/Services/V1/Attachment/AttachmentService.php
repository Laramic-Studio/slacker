<?php

namespace App\Services\V1\Attachment;

use Illuminate\Http\Request;

class AttachmentService
{

    public function uploadFile(string $fileName, string $folder): array | null
    {
        $path = storeImage($fileName, $folder);
        return [
            'file_url' => public_path($path),
            'file_name' => $fileName,
        ];
    }
}
