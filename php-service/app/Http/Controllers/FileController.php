<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\File;
use App\Jobs\TriggerTransformationJob;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller
{
    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:10240|mimes:csv,xlsx,json',
        ]);
    
        $file = $request->file('file');
        $filename = uniqid().'_'.$file->getClientOriginalName();
        $fileSize = $file->getSize(); // Get the file size before moving
        $destinationPath = env('FILES_PATH', '/app/uploads'); // Shared volume path
        $file->move($destinationPath, $filename); 
    
        $filePath = $destinationPath . '/' . $filename; // Full path to the file
    
        \Log::info('File stored at: ' . $filePath);
    
        $meta = File::create([
            'name' => $filename,
            'type' => $file->getClientMimeType(),
            'size' => $fileSize, 
            'uploader_ip' => $request->ip(),
        ]);
        \Log::info($meta);
    
        /**
         * here you can use queue driver for async processing(e.g. Redis, SQS)
         * by using queue.php config file and delay it using delay() method
         */
        TriggerTransformationJob::dispatch($meta->file_id, $filePath);
    
        return response()->json(['file_id' => $meta->file_id, 'status' => 'uploaded']);
    }

    public function status($id)
    {
        $file = File::findOrFail($id);
        return response()->json([
            'file_id' => $file->file_id,
            'status' => $file->status,
            'result_path' => $file->result_path,
        ]);
    }

    public function updateStatus(Request $request)
    {
        $request->validate([
            'file_id' => 'required|integer',
            'status' => 'required|string',
            'result_path' => 'nullable|string',
        ]);

        $file = File::findOrFail($request->file_id);
        $file->update([
            'status' => $request->status,
            'result_path' => $request->result_path,
        ]);

        return response()->json(['message' => 'Status updated']);
    }
}

