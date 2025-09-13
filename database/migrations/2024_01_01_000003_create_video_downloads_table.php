<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('video_downloads', function (Blueprint $table) {
            $table->id();
            $table->string('youtube_url')->comment('Original YouTube URL');
            $table->string('video_title')->nullable()->comment('Video title from YouTube');
            $table->string('video_id')->comment('YouTube video ID');
            $table->enum('format', ['mp3', 'mp4', 'wav'])->comment('Requested download format');
            $table->enum('status', ['pending', 'processing', 'completed', 'failed'])->default('pending');
            $table->string('file_path')->nullable()->comment('Path to downloaded/converted file');
            $table->integer('file_size')->nullable()->comment('File size in bytes');
            $table->integer('duration')->nullable()->comment('Video duration in seconds');
            $table->text('error_message')->nullable()->comment('Error message if failed');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            // Indexes for performance
            $table->index('video_id');
            $table->index('status');
            $table->index(['status', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('video_downloads');
    }
};