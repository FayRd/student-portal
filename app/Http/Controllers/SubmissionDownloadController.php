<?php

namespace App\Http\Controllers;

use App\Models\Submission;
use Illuminate\Support\Facades\Storage;

class SubmissionDownloadController extends Controller
{
    public function download(Submission $submission)
    {
        if (! Storage::disk(config('filesystems.resource_disk', 'local'))->exists($submission->file_path)) {
            return back()->with('download_error', true);
        }

        return Storage::disk(config('filesystems.resource_disk', 'local'))
            ->download($submission->file_path, $submission->file_name);
    }
}
