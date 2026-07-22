<?php

namespace App\Http\Controllers;

use App\Models\ModuleResource;
use Illuminate\Support\Facades\Storage;

class ResourceDownloadController extends Controller
{
    public function download(ModuleResource $resource)
    {
        if (! Storage::disk('local')->exists($resource->file_path)) {
            return back()->with('download_error', true);
        }

        return Storage::disk('local')->download(
            $resource->file_path,
            $resource->file_name
        );
    }
}
