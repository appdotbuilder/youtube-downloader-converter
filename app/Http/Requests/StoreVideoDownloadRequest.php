<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreVideoDownloadRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'youtube_url' => [
                'required',
                'string',
                'url',
                'regex:/^(https?:\/\/)?(www\.)?(youtube\.com\/(watch\?v=|embed\/|v\/)|youtu\.be\/)[\w\-]+(&[\w=]*)?$/'
            ],
            'format' => 'required|in:mp3,mp4,wav',
        ];
    }

    /**
     * Get custom error messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'youtube_url.required' => 'Please enter a YouTube URL.',
            'youtube_url.url' => 'Please enter a valid URL.',
            'youtube_url.regex' => 'Please enter a valid YouTube URL.',
            'format.required' => 'Please select a format.',
            'format.in' => 'Please select a valid format (MP3, MP4, or WAV).',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Clean up the YouTube URL
        if ($this->has('youtube_url')) {
            $url = $this->input('youtube_url');
            
            // Handle different YouTube URL formats
            if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/embed\/)([^&\n?#]+)/', $url, $matches)) {
                $videoId = $matches[1];
                $this->merge([
                    'youtube_url' => "https://www.youtube.com/watch?v={$videoId}"
                ]);
            }
        }
    }

    /**
     * Get the video ID from the YouTube URL.
     *
     * @return string|null
     */
    public function getVideoId(): ?string
    {
        $url = $this->input('youtube_url');
        
        if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/embed\/)([^&\n?#]+)/', $url, $matches)) {
            return $matches[1];
        }
        
        return null;
    }
}