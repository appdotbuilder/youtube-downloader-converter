<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreVideoDownloadRequest;
use App\Models\VideoDownload;
use App\Services\YouTubeDownloadService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class VideoDownloadController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct(
        private YouTubeDownloadService $downloadService
    ) {}

    /**
     * Display the video download page.
     */
    public function index()
    {
        $downloads = $this->downloadService->getAllDownloads();
        
        $downloadData = $downloads->map(function ($download) {
            /** @var VideoDownload $download */
            return [
                'id' => $download->id,
                'youtube_url' => $download->youtube_url,
                'video_title' => $download->video_title,
                'video_id' => $download->video_id,
                'format' => $download->format,
                'status' => $download->status,
                'file_size' => $download->formatted_file_size,
                'duration' => $download->formatted_duration,
                'error_message' => $download->error_message,
                'created_at' => $download->created_at?->format('M j, Y g:i A'),
                'completed_at' => $download->completed_at?->format('M j, Y g:i A'),
            ];
        });
        
        return Inertia::render('welcome', [
            'downloads' => $downloadData
        ]);
    }

    /**
     * Start a new video download.
     */
    public function store(StoreVideoDownloadRequest $request)
    {
        try {
            $download = $this->downloadService->createDownload(
                $request->input('youtube_url'),
                $request->input('format')
            );

            return redirect()->route('downloads.index')
                ->with('success', 'Download started successfully! Processing...');

        } catch (\InvalidArgumentException $e) {
            return redirect()->back()
                ->withErrors(['youtube_url' => $e->getMessage()])
                ->withInput();
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to start download. Please try again.')
                ->withInput();
        }
    }

    /**
     * Display the specified video download.
     */
    public function show(VideoDownload $download)
    {
        return Inertia::render('downloads/show', [
            'download' => [
                'id' => $download->id,
                'youtube_url' => $download->youtube_url,
                'video_title' => $download->video_title,
                'video_id' => $download->video_id,
                'format' => $download->format,
                'status' => $download->status,
                'file_path' => $download->file_path,
                'file_size' => $download->formatted_file_size,
                'duration' => $download->formatted_duration,
                'error_message' => $download->error_message,
                'created_at' => $download->created_at?->format('M j, Y g:i A'),
                'started_at' => $download->started_at?->format('M j, Y g:i A'),
                'completed_at' => $download->completed_at?->format('M j, Y g:i A'),
            ]
        ]);
    }

    /**
     * Remove the specified video download.
     */
    public function destroy(VideoDownload $download)
    {
        try {
            $this->downloadService->deleteDownload($download);
            
            return redirect()->route('downloads.index')
                ->with('success', 'Download deleted successfully.');
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to delete download. Please try again.');
        }
    }


}