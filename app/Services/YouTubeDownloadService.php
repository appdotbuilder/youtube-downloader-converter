<?php

namespace App\Services;

use App\Models\VideoDownload;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class YouTubeDownloadService
{
    /**
     * Create a new video download record and start processing.
     *
     * @param string $youtubeUrl
     * @param string $format
     * @return VideoDownload
     */
    public function createDownload(string $youtubeUrl, string $format): VideoDownload
    {
        $videoId = $this->extractVideoId($youtubeUrl);
        
        if (!$videoId) {
            throw new \InvalidArgumentException('Invalid YouTube URL');
        }

        // Check if this video is already being downloaded in the same format
        $existingDownload = VideoDownload::where('video_id', $videoId)
            ->where('format', $format)
            ->whereIn('status', ['pending', 'processing', 'completed'])
            ->first();

        if ($existingDownload) {
            return $existingDownload;
        }

        $download = VideoDownload::create([
            'youtube_url' => $youtubeUrl,
            'video_id' => $videoId,
            'format' => $format,
            'status' => 'pending',
        ]);

        // In a real application, you would dispatch this to a queue
        // For this example, we'll simulate the process
        $this->simulateDownloadProcess($download);

        return $download;
    }

    /**
     * Extract video ID from YouTube URL.
     *
     * @param string $url
     * @return string|null
     */
    public function extractVideoId(string $url): ?string
    {
        if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/embed\/)([^&\n?#]+)/', $url, $matches)) {
            return $matches[1];
        }
        
        return null;
    }

    /**
     * Simulate the download and conversion process.
     * In a real application, this would use yt-dlp or similar tool.
     *
     * @param VideoDownload $download
     * @return void
     */
    protected function simulateDownloadProcess(VideoDownload $download): void
    {
        try {
            $download->update([
                'status' => 'processing',
                'started_at' => now(),
            ]);

            // Simulate getting video information
            $videoInfo = $this->simulateVideoInfo($download->video_id);
            $download->update([
                'video_title' => $videoInfo['title'],
                'duration' => $videoInfo['duration'],
            ]);

            // Simulate download/conversion time based on format
            $processingTime = match($download->format) {
                'mp3' => random_int(5, 15), // 5-15 seconds
                'wav' => random_int(10, 25), // 10-25 seconds  
                'mp4' => random_int(15, 30), // 15-30 seconds
                default => random_int(10, 20), // Default for any other format
            };

            // In a real app, this would be handled by a queued job
            // For demo purposes, we'll just simulate success
            $fileName = $this->generateFileName($download->video_title, $download->format);
            $filePath = "downloads/{$fileName}";
            
            // Simulate file creation
            $fileSize = $this->simulateFileSize($download->format, $download->duration);
            
            $download->update([
                'status' => 'completed',
                'file_path' => $filePath,
                'file_size' => $fileSize,
                'completed_at' => now(),
            ]);

            Log::info("Video download completed", [
                'video_id' => $download->video_id,
                'format' => $download->format,
                'file_size' => $fileSize,
            ]);

        } catch (\Exception $e) {
            $download->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
                'completed_at' => now(),
            ]);

            Log::error("Video download failed", [
                'video_id' => $download->video_id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Simulate getting video information from YouTube.
     *
     * @param string $videoId
     * @return array
     */
    protected function simulateVideoInfo(string $videoId): array
    {
        $titles = [
            'Amazing Tutorial - How to Build Modern Web Apps',
            'Music Video - Best Hits 2024',
            'Documentary: The Future of Technology',
            'Cooking Tutorial: Delicious Recipes',
            'Travel Vlog: Beautiful Destinations',
            'Educational Content: Science Explained',
            'Entertainment: Comedy Sketches',
            'News Update: Latest Headlines',
        ];

        return [
            'title' => $titles[array_rand($titles)],
            'duration' => random_int(30, 3600), // 30 seconds to 1 hour
        ];
    }

    /**
     * Generate a safe filename for the downloaded file.
     *
     * @param string $title
     * @param string $format
     * @return string
     */
    protected function generateFileName(string $title, string $format): string
    {
        $slug = Str::slug($title);
        $uuid = Str::uuid();
        
        return "{$slug}_{$uuid}.{$format}";
    }

    /**
     * Simulate file size based on format and duration.
     *
     * @param string $format
     * @param int $duration
     * @return int
     */
    protected function simulateFileSize(string $format, int $duration): int
    {
        // Approximate file size calculation (bytes per second)
        $bytesPerSecond = match($format) {
            'mp3' => 16000,  // ~128kbps
            'wav' => 176400, // ~1411kbps (CD quality)
            'mp4' => 125000, // ~1Mbps video
            default => 50000, // Default for other formats
        };

        return $duration * $bytesPerSecond;
    }

    /**
     * Get all downloads with their current status.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllDownloads()
    {
        return VideoDownload::latest()->get();
    }

    /**
     * Get download by ID.
     *
     * @param int $id
     * @return VideoDownload|null
     */
    public function getDownload(int $id): ?VideoDownload
    {
        return VideoDownload::find($id);
    }

    /**
     * Delete a download and its associated file.
     *
     * @param VideoDownload $download
     * @return bool
     */
    public function deleteDownload(VideoDownload $download): bool
    {
        if ($download->file_path && Storage::exists($download->file_path)) {
            Storage::delete($download->file_path);
        }

        return $download->delete();
    }
}