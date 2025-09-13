<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\VideoDownload;
use Illuminate\Http\Request;

class DownloadStatusController extends Controller
{
    /**
     * Get the status of multiple downloads (for polling).
     */
    public function index(Request $request)
    {
        $ids = $request->input('ids', []);
        
        if (empty($ids)) {
            return response()->json(['downloads' => []]);
        }

        // Convert comma-separated string to array if needed
        if (is_string($ids)) {
            $ids = explode(',', $ids);
        }

        $downloads = VideoDownload::whereIn('id', $ids)->get();
        
        $downloadData = $downloads->map(function ($download) {
            /** @var VideoDownload $download */
            return [
                'id' => $download->id,
                'status' => $download->status,
                'video_title' => $download->video_title,
                'file_size' => $download->formatted_file_size,
                'duration' => $download->formatted_duration,
                'error_message' => $download->error_message,
                'completed_at' => $download->completed_at?->format('M j, Y g:i A'),
            ];
        });
        
        return response()->json([
            'downloads' => $downloadData
        ]);
    }
}