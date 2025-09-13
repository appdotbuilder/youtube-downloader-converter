<?php

namespace Database\Seeders;

use App\Models\VideoDownload;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class VideoDownloadSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        // Create some sample downloads for demonstration
        VideoDownload::factory()->count(3)->completed()->create();
        VideoDownload::factory()->count(2)->pending()->create();
        VideoDownload::factory()->count(1)->failed()->create();
    }
}