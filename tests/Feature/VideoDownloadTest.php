<?php

use App\Models\VideoDownload;
use App\Services\YouTubeDownloadService;

it('displays homepage correctly', function () {
    $response = $this->get('/');
    $response->assertStatus(200);
});

it('can create video download', function () {
    $response = $this->post('/downloads', [
        'youtube_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
        'format' => 'mp3',
    ]);

    $response->assertRedirect(route('downloads.index'));
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('video_downloads', [
        'video_id' => 'dQw4w9WgXcQ',
        'format' => 'mp3',
    ]);
});

it('validates youtube url format', function () {
    $response = $this->post('/downloads', [
        'youtube_url' => 'https://example.com/invalid',
        'format' => 'mp3',
    ]);

    $response->assertSessionHasErrors('youtube_url');
});

it('validates required fields', function () {
    $response = $this->post('/downloads', []);
    $response->assertSessionHasErrors(['youtube_url', 'format']);
});

it('validates format options', function () {
    $response = $this->post('/downloads', [
        'youtube_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
        'format' => 'invalid',
    ]);

    $response->assertSessionHasErrors('format');
});

it('can delete video download', function () {
    $download = VideoDownload::factory()->create();

    $response = $this->delete(route('downloads.destroy', $download));

    $response->assertRedirect(route('downloads.index'));
    $response->assertSessionHas('success');
    $this->assertDatabaseMissing('video_downloads', ['id' => $download->id]);
});

it('can view download details', function () {
    $download = VideoDownload::factory()->completed()->create();

    $response = $this->get(route('downloads.show', $download));
    $response->assertStatus(200);
});

it('download service creates download', function () {
    $service = app(YouTubeDownloadService::class);
    
    $download = $service->createDownload(
        'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
        'mp4'
    );

    expect($download)->toBeInstanceOf(VideoDownload::class);
    expect($download->video_id)->toBe('dQw4w9WgXcQ');
    expect($download->format)->toBe('mp4');
    
    $this->assertDatabaseHas('video_downloads', [
        'video_id' => 'dQw4w9WgXcQ',
        'format' => 'mp4',
    ]);
});

it('download service prevents duplicate downloads', function () {
    $service = app(YouTubeDownloadService::class);
    
    // Create first download
    $download1 = $service->createDownload(
        'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
        'mp3'
    );

    // Try to create duplicate
    $download2 = $service->createDownload(
        'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
        'mp3'
    );

    // Should return the same download
    expect($download2->id)->toBe($download1->id);
    
    // Should only have one record in database
    expect(VideoDownload::where('video_id', 'dQw4w9WgXcQ')->where('format', 'mp3')->count())->toBe(1);
});

it('status endpoint returns download status', function () {
    $download1 = VideoDownload::factory()->pending()->create();
    $download2 = VideoDownload::factory()->completed()->create();

    $response = $this->get("/downloads/status/check?ids={$download1->id},{$download2->id}");

    $response->assertStatus(200);
    $response->assertJsonStructure([
        'downloads' => [
            '*' => ['id', 'status', 'video_title']
        ]
    ]);

    $data = $response->json();
    expect(count($data['downloads']))->toBe(2);
});

it('supports different youtube url formats', function () {
    $urls = [
        'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
        'https://youtu.be/dQw4w9WgXcQ', 
        'https://youtube.com/watch?v=dQw4w9WgXcQ',
    ];

    $formats = ['mp3', 'mp4', 'wav']; // Use different formats to avoid duplicates

    foreach ($urls as $index => $url) {
        $response = $this->post('/downloads', [
            'youtube_url' => $url,
            'format' => $formats[$index],
        ]);

        $response->assertRedirect(route('downloads.index'));
    }

    // Should all resolve to the same video ID but different formats
    expect(VideoDownload::where('video_id', 'dQw4w9WgXcQ')->count())->toBe(3);
});