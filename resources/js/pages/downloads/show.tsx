import React from 'react';
import { Head, Link, router } from '@inertiajs/react';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { ExternalLink, Download, Trash2, ArrowLeft } from '@/components/icons';

interface Download {
    id: number;
    youtube_url: string;
    video_title: string | null;
    video_id: string;
    format: string;
    status: 'pending' | 'processing' | 'completed' | 'failed';
    file_path: string | null;
    file_size: string;
    duration: string;
    error_message: string | null;
    created_at: string;
    started_at: string | null;
    completed_at: string | null;
}

interface Props {
    download: Download;
    [key: string]: unknown;
}

export default function ShowDownload({ download }: Props) {
    const handleDelete = () => {
        if (confirm('Are you sure you want to delete this download?')) {
            router.delete(route('downloads.destroy', download.id));
        }
    };

    const getStatusBadge = (status: Download['status']) => {
        const variants: Record<string, "default" | "secondary" | "destructive" | "outline"> = {
            'pending': 'outline',
            'processing': 'default',
            'completed': 'secondary',
            'failed': 'destructive',
        };

        return (
            <Badge variant={variants[status] || 'default'} className="capitalize">
                {status}
            </Badge>
        );
    };

    const getFormatIcon = (format: string) => {
        switch (format.toLowerCase()) {
            case 'mp3':
                return '🎵';
            case 'mp4':
                return '🎬';
            case 'wav':
                return '🔊';
            default:
                return '📄';
        }
    };

    return (
        <>
            <Head title={`Download: ${download.video_title || download.video_id}`} />
            
            <div className="min-h-screen bg-gray-50">
                {/* Header */}
                <div className="bg-white shadow-sm border-b">
                    <div className="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                        <div className="flex items-center justify-between py-4">
                            <div className="flex items-center gap-4">
                                <Link
                                    href={route('downloads.index')}
                                    className="flex items-center gap-2 text-gray-600 hover:text-gray-900"
                                >
                                    <ArrowLeft size={20} />
                                    Back to Downloads
                                </Link>
                            </div>
                            <div className="flex items-center gap-2">
                                <Button
                                    variant="outline"
                                    onClick={() => window.open(download.youtube_url, '_blank')}
                                >
                                    <ExternalLink size={16} className="mr-2" />
                                    View on YouTube
                                </Button>
                                <Button
                                    variant="destructive"
                                    onClick={handleDelete}
                                >
                                    <Trash2 size={16} className="mr-2" />
                                    Delete
                                </Button>
                            </div>
                        </div>
                    </div>
                </div>

                {/* Content */}
                <div className="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                    <Card>
                        <CardHeader>
                            <div className="flex items-start justify-between">
                                <div>
                                    <CardTitle className="text-xl mb-2">
                                        {download.video_title || `Video ${download.video_id}`}
                                    </CardTitle>
                                    <div className="flex items-center gap-4 text-sm text-gray-600">
                                        {getStatusBadge(download.status)}
                                        <span className="flex items-center gap-1">
                                            {getFormatIcon(download.format)} {download.format.toUpperCase()}
                                        </span>
                                        {download.duration !== 'Unknown' && (
                                            <span>Duration: {download.duration}</span>
                                        )}
                                        {download.file_size !== 'Unknown' && (
                                            <span>Size: {download.file_size}</span>
                                        )}
                                    </div>
                                </div>
                            </div>
                        </CardHeader>

                        <CardContent className="space-y-6">
                            {/* Status Information */}
                            <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <h3 className="font-semibold mb-2">Download Information</h3>
                                    <div className="space-y-2 text-sm">
                                        <div className="flex justify-between">
                                            <span className="text-gray-600">Video ID:</span>
                                            <span className="font-mono">{download.video_id}</span>
                                        </div>
                                        <div className="flex justify-between">
                                            <span className="text-gray-600">Format:</span>
                                            <span>{download.format.toUpperCase()}</span>
                                        </div>
                                        <div className="flex justify-between">
                                            <span className="text-gray-600">Status:</span>
                                            {getStatusBadge(download.status)}
                                        </div>
                                        {download.file_path && (
                                            <div className="flex justify-between">
                                                <span className="text-gray-600">File Path:</span>
                                                <span className="font-mono text-xs">{download.file_path}</span>
                                            </div>
                                        )}
                                    </div>
                                </div>

                                <div>
                                    <h3 className="font-semibold mb-2">Timeline</h3>
                                    <div className="space-y-2 text-sm">
                                        <div className="flex justify-between">
                                            <span className="text-gray-600">Created:</span>
                                            <span>{download.created_at}</span>
                                        </div>
                                        {download.started_at && (
                                            <div className="flex justify-between">
                                                <span className="text-gray-600">Started:</span>
                                                <span>{download.started_at}</span>
                                            </div>
                                        )}
                                        {download.completed_at && (
                                            <div className="flex justify-between">
                                                <span className="text-gray-600">Completed:</span>
                                                <span>{download.completed_at}</span>
                                            </div>
                                        )}
                                    </div>
                                </div>
                            </div>

                            {/* Error Message */}
                            {download.status === 'failed' && download.error_message && (
                                <div className="p-4 bg-red-50 border border-red-200 rounded-md">
                                    <h4 className="font-semibold text-red-900 mb-2">Error Details</h4>
                                    <p className="text-sm text-red-800">{download.error_message}</p>
                                </div>
                            )}

                            {/* Processing Status */}
                            {download.status === 'processing' && (
                                <div className="p-4 bg-blue-50 border border-blue-200 rounded-md">
                                    <div className="flex items-center gap-2">
                                        <div className="animate-spin rounded-full h-4 w-4 border-b-2 border-blue-600"></div>
                                        <span className="text-blue-900 font-medium">Processing...</span>
                                    </div>
                                    <p className="text-sm text-blue-800 mt-1">
                                        Your video is being downloaded and converted. This page will update automatically.
                                    </p>
                                </div>
                            )}

                            {/* Success Status */}
                            {download.status === 'completed' && (
                                <div className="p-4 bg-green-50 border border-green-200 rounded-md">
                                    <div className="flex items-center justify-between">
                                        <div>
                                            <h4 className="font-semibold text-green-900">Download Complete!</h4>
                                            <p className="text-sm text-green-800">
                                                Your {download.format.toUpperCase()} file is ready.
                                            </p>
                                        </div>
                                        <Button className="bg-green-600 hover:bg-green-700">
                                            <Download size={16} className="mr-2" />
                                            Download File
                                        </Button>
                                    </div>
                                </div>
                            )}

                            {/* YouTube Embed Preview */}
                            <div>
                                <h3 className="font-semibold mb-2">Video Preview</h3>
                                <div className="aspect-video bg-gray-100 rounded-md overflow-hidden">
                                    <iframe
                                        src={`https://www.youtube.com/embed/${download.video_id}`}
                                        className="w-full h-full"
                                        allowFullScreen
                                        title={download.video_title || 'YouTube Video'}
                                    />
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </>
    );
}