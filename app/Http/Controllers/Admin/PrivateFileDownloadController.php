<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PrivateFileDownloadController extends Controller
{
    public function download(Request $request)
    {
        if (! $request->file) {
            abort(404);
        }

        $file = Storage::disk('private')->readStream($request->file);

        return response()->stream(function() use ($file) {
            fpassthru($file);
        }, 200, ['Content-Disposition' => 'attachment; filename="' . substr(strrchr($request->file, '/'), 1) . '"']);
    }
}
