<?php

namespace Database\Factories;

use App\Models\VideoDownload;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\VideoDownload>
 */
class VideoDownloadFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<VideoDownload>
     */
    protected $model = VideoDownload::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $videoId = fake()->regexify('[a-zA-Z0-9_-]{11}');
        $formats = ['mp3', 'mp4', 'wav'];
        $statuses = ['pending', 'processing', 'completed', 'failed'];
        
        return [
            'youtube_url' => "https://www.youtube.com/watch?v={$videoId}",
            'video_title' => fake()->sentence(3, true),
            'video_id' => $videoId,
            'format' => fake()->randomElement($formats),
            'status' => fake()->randomElement($statuses),
            'file_size' => fake()->numberBetween(1000000, 100000000), // 1MB to 100MB
            'duration' => fake()->numberBetween(30, 3600), // 30 seconds to 1 hour
            'created_at' => fake()->dateTimeBetween('-1 month', 'now'),
        ];
    }

    /**
     * Indicate that the download is pending.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
            'file_path' => null,
            'file_size' => null,
            'started_at' => null,
            'completed_at' => null,
            'error_message' => null,
        ]);
    }

    /**
     * Indicate that the download is completed.
     */
    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'completed',
            'file_path' => 'downloads/' . fake()->uuid() . '.' . $attributes['format'],
            'started_at' => fake()->dateTimeBetween('-1 hour', '-30 minutes'),
            'completed_at' => fake()->dateTimeBetween('-30 minutes', 'now'),
            'error_message' => null,
        ]);
    }

    /**
     * Indicate that the download failed.
     */
    public function failed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'failed',
            'file_path' => null,
            'file_size' => null,
            'started_at' => fake()->dateTimeBetween('-1 hour', '-30 minutes'),
            'completed_at' => null,
            'error_message' => fake()->sentence(),
        ]);
    }
}