<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\VideoDownload
 *
 * @property int $id
 * @property string $youtube_url
 * @property string|null $video_title
 * @property string $video_id
 * @property string $format
 * @property string $status
 * @property string|null $file_path
 * @property int|null $file_size
 * @property int|null $duration
 * @property string|null $error_message
 * @property \Illuminate\Support\Carbon|null $started_at
 * @property \Illuminate\Support\Carbon|null $completed_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * 
 * @method static \Illuminate\Database\Eloquent\Builder|VideoDownload newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|VideoDownload newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|VideoDownload query()
 * @method static \Illuminate\Database\Eloquent\Builder|VideoDownload whereCompletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VideoDownload whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VideoDownload whereDuration($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VideoDownload whereErrorMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VideoDownload whereFileSize($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VideoDownload whereFilePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VideoDownload whereFormat($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VideoDownload whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VideoDownload whereStartedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VideoDownload whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VideoDownload whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VideoDownload whereVideoId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VideoDownload whereVideoTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VideoDownload whereYoutubeUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VideoDownload pending()
 * @method static \Illuminate\Database\Eloquent\Builder|VideoDownload completed()
 * @method static \Database\Factories\VideoDownloadFactory factory($count = null, $state = [])
 * 
 * @mixin \Eloquent
 */
class VideoDownload extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'youtube_url',
        'video_title',
        'video_id',
        'format',
        'status',
        'file_path',
        'file_size',
        'duration',
        'error_message',
        'started_at',
        'completed_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'file_size' => 'integer',
        'duration' => 'integer',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Scope a query to only include pending downloads.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope a query to only include completed downloads.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Get the file size in human-readable format.
     *
     * @return string
     */
    public function getFormattedFileSizeAttribute(): string
    {
        if (!$this->file_size) {
            return 'Unknown';
        }

        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Get the duration in human-readable format.
     *
     * @return string
     */
    public function getFormattedDurationAttribute(): string
    {
        if (!$this->duration) {
            return 'Unknown';
        }

        $minutes = floor($this->duration / 60);
        $seconds = $this->duration % 60;

        return sprintf('%d:%02d', $minutes, $seconds);
    }
}