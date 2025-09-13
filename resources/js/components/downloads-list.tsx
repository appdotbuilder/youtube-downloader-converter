import React, { useState, useEffect } from 'react';
import { router } from '@inertiajs/react';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Progress } from '@/components/ui/progress';
import { Trash2, ExternalLink, RefreshCw, Download, Clock, CheckCircle, XCircle, AlertCircle } from '@/components/icons';

interface Download {
    id: number;
    youtube_url: string;
    video_title: string | null;
    video_id: string;
    format: string;
    status: 'pending' | 'processing' | 'completed' | 'failed';
    file_size: string;
    duration: string;
    error_message: string | null;
    created_at: string;
    completed_at: string | null;
}

interface DownloadsListProps {
    downloads: Download[];
}

export function DownloadsList({ downloads: initialDownloads }: DownloadsListProps) {
    const [downloads, setDownloads] = useState<Download[]>(initialDownloads);
    const [isPolling, setIsPolling] = useState(false);

    useEffect(() => {
        setDownloads(initialDownloads);
    }, [initialDownloads]);

    // Poll for status updates of pending/processing downloads
    useEffect(() => {
        const pendingDownloads = downloads.filter(d => 
            d.status === 'pending' || d.status === 'processing'
        );

        if (pendingDownloads.length === 0) {
            setIsPolling(false);
            return;
        }

        setIsPolling(true);
        const interval = setInterval(() => {
            const ids = pendingDownloads.map(d => d.id);
            
            fetch(`/downloads/status/check?ids=${ids.join(',')}`)
                .then(response => response.json())
                .then((data: { downloads: Download[] }) => {
                    setDownloads(prev => 
                        prev.map(download => {
                            const updated = data.downloads.find((d: Download) => d.id === download.id);
                            return updated ? { ...download, ...updated } : download;
                        })
                    );
                })
                .catch(console.error);
        }, 2000); // Poll every 2 seconds

        return () => clearInterval(interval);
    }, [downloads]);

    const handleDelete = (downloadId: number) => {
        if (confirm('Are you sure you want to delete this download?')) {
            router.delete(route('downloads.destroy', downloadId), {
                preserveScroll: true,
            });
        }
    };

    const getStatusIcon = (status: Download['status']) => {
        switch (status) {
            case 'pending':
                return <Clock className="h-4 w-4 text-yellow-500" />;
            case 'processing':
                return <RefreshCw className="h-4 w-4 text-blue-500 animate-spin" />;
            case 'completed':
                return <CheckCircle className="h-4 w-4 text-green-500" />;
            case 'failed':
                return <XCircle className="h-4 w-4 text-red-500" />;
            default:
                return <AlertCircle className="h-4 w-4 text-gray-500" />;
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
                {getStatusIcon(status)}
                <span className="ml-1">{status}</span>
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

    if (downloads.length === 0) {
        return (
            <div className="text-center py-12">
                <Download className="h-12 w-12 text-gray-400 mx-auto mb-4" />
                <h3 className="text-lg font-medium text-gray-900 mb-2">No downloads yet</h3>
                <p className="text-gray-500">Start by pasting a YouTube URL above</p>
            </div>
        );
    }

    return (
        <div className="space-y-4">
            <div className="flex items-center justify-between">
                <h2 className="text-xl font-semibold">Downloads ({downloads.length})</h2>
                {isPolling && (
                    <div className="flex items-center gap-2 text-sm text-blue-600">
                        <RefreshCw className="h-4 w-4 animate-spin" />
                        Updating...
                    </div>
                )}
            </div>

            <div className="grid gap-4">
                {downloads.map((download) => (
                    <Card key={download.id} className="overflow-hidden">
                        <CardHeader className="pb-3">
                            <div className="flex items-start justify-between">
                                <div className="flex-1">
                                    <CardTitle className="text-base line-clamp-1">
                                        {download.video_title || `Video ${download.video_id}`}
                                    </CardTitle>
                                    <div className="flex items-center gap-2 mt-1">
                                        {getStatusBadge(download.status)}
                                        <span className="text-sm text-muted-foreground">
                                            {getFormatIcon(download.format)} {download.format.toUpperCase()}
                                        </span>
                                        {download.duration !== 'Unknown' && (
                                            <span className="text-sm text-muted-foreground">
                                                {download.duration}
                                            </span>
                                        )}
                                        {download.file_size !== 'Unknown' && (
                                            <span className="text-sm text-muted-foreground">
                                                {download.file_size}
                                            </span>
                                        )}
                                    </div>
                                </div>
                                <div className="flex items-center gap-2">
                                    <Button
                                        variant="ghost"
                                        size="sm"
                                        onClick={() => window.open(download.youtube_url, '_blank')}
                                    >
                                        <ExternalLink className="h-4 w-4" />
                                    </Button>
                                    <Button
                                        variant="ghost"
                                        size="sm"
                                        onClick={() => handleDelete(download.id)}
                                    >
                                        <Trash2 className="h-4 w-4" />
                                    </Button>
                                </div>
                            </div>
                        </CardHeader>

                        <CardContent className="pt-0">
                            {download.status === 'processing' && (
                                <div className="mb-3">
                                    <Progress value={50} className="h-2" />
                                    <p className="text-sm text-muted-foreground mt-1">
                                        Processing video...
                                    </p>
                                </div>
                            )}

                            {download.status === 'failed' && download.error_message && (
                                <div className="mb-3 p-3 bg-red-50 border border-red-200 rounded-md">
                                    <p className="text-sm text-red-800">
                                        <strong>Error:</strong> {download.error_message}
                                    </p>
                                </div>
                            )}

                            <div className="flex items-center justify-between text-sm text-muted-foreground">
                                <span>Started: {download.created_at}</span>
                                {download.completed_at && (
                                    <span>Completed: {download.completed_at}</span>
                                )}
                            </div>
                        </CardContent>
                    </Card>
                ))}
            </div>
        </div>
    );
}