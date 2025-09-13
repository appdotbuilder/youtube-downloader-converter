import React from 'react';
import { useForm } from '@inertiajs/react';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Download, Youtube } from '@/components/icons';

interface VideoDownloadFormData {
    youtube_url: string;
    format: string;
    [key: string]: string;
}

export function VideoDownloadForm() {
    const { data, setData, post, processing, errors } = useForm<VideoDownloadFormData>({
        youtube_url: '',
        format: '' as string,
    });

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        post(route('downloads.store'), {
            preserveScroll: true,
        });
    };

    return (
        <Card className="w-full max-w-2xl mx-auto">
            <CardHeader className="text-center">
                <div className="flex items-center justify-center gap-2 mb-4">
                    <Youtube className="h-8 w-8 text-red-500" />
                    <CardTitle className="text-2xl">YouTube Video Downloader</CardTitle>
                </div>
                <CardDescription>
                    Download and convert YouTube videos to MP3, MP4, or WAV format
                </CardDescription>
            </CardHeader>
            <CardContent>
                <form onSubmit={handleSubmit} className="space-y-6">
                    <div className="space-y-2">
                        <Label htmlFor="youtube_url">YouTube URL</Label>
                        <Input
                            id="youtube_url"
                            type="url"
                            placeholder="https://www.youtube.com/watch?v=..."
                            value={data.youtube_url}
                            onChange={(e) => setData('youtube_url', e.target.value)}
                            className={errors.youtube_url ? 'border-red-500' : ''}
                        />
                        {errors.youtube_url && (
                            <p className="text-sm text-red-500">{errors.youtube_url}</p>
                        )}
                        <p className="text-xs text-muted-foreground">
                            Supports youtube.com and youtu.be links
                        </p>
                    </div>

                    <div className="space-y-2">
                        <Label htmlFor="format">Output Format</Label>
                        <Select 
                            value={data.format} 
                            onValueChange={(value: string) => setData('format', value)}
                        >
                            <SelectTrigger className={errors.format ? 'border-red-500' : ''}>
                                <SelectValue placeholder="Choose format" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem value="mp3">
                                    <div className="flex items-center gap-2">
                                        <span>🎵</span>
                                        <div>
                                            <div className="font-medium">MP3</div>
                                            <div className="text-xs text-muted-foreground">Audio only, smaller file</div>
                                        </div>
                                    </div>
                                </SelectItem>
                                <SelectItem value="mp4">
                                    <div className="flex items-center gap-2">
                                        <span>🎬</span>
                                        <div>
                                            <div className="font-medium">MP4</div>
                                            <div className="text-xs text-muted-foreground">Video with audio</div>
                                        </div>
                                    </div>
                                </SelectItem>
                                <SelectItem value="wav">
                                    <div className="flex items-center gap-2">
                                        <span>🔊</span>
                                        <div>
                                            <div className="font-medium">WAV</div>
                                            <div className="text-xs text-muted-foreground">High quality audio</div>
                                        </div>
                                    </div>
                                </SelectItem>
                            </SelectContent>
                        </Select>
                        {errors.format && (
                            <p className="text-sm text-red-500">{errors.format}</p>
                        )}
                    </div>

                    <Button 
                        type="submit" 
                        disabled={processing || !data.youtube_url || !data.format}
                        className="w-full"
                        size="lg"
                    >
                        {processing ? (
                            <>
                                <div className="animate-spin rounded-full h-4 w-4 border-b-2 border-white mr-2"></div>
                                Processing...
                            </>
                        ) : (
                            <>
                                <Download className="h-4 w-4 mr-2" />
                                Start Download
                            </>
                        )}
                    </Button>
                </form>

                <div className="mt-6 p-4 bg-blue-50 rounded-lg border border-blue-200">
                    <h4 className="font-medium text-blue-900 mb-2">✨ Features:</h4>
                    <ul className="text-sm text-blue-800 space-y-1">
                        <li>• Multiple format support (MP3, MP4, WAV)</li>
                        <li>• Simultaneous downloads</li>
                        <li>• Real-time progress tracking</li>
                        <li>• Download history</li>
                    </ul>
                </div>
            </CardContent>
        </Card>
    );
}