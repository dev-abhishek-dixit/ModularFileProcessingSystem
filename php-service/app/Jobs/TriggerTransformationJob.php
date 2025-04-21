<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

class TriggerTransformationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $fileId;
    protected $filePath;

    public function __construct($fileId, $filePath)
    {
        $this->fileId = $fileId;
        $this->filePath = $filePath;
    }

    public function handle()
    {
        $response = Http::withToken(env('API_TOKEN'))->post(env('PYTHON_TRANSFORM_URL'), [
            'file_id' => $this->fileId,
            'file_path' => $this->filePath,
        ]);
        \Log::info('TriggerTransformationJob Response', [
            'status' => $response->status(),
            'body' => $response->body(),
        ]);
        
    }
}

