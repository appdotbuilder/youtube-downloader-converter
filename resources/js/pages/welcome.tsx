import React from 'react';
import { Head, Link, usePage } from '@inertiajs/react';
import { VideoDownloadForm } from '@/components/video-download-form';
import { DownloadsList } from '@/components/downloads-list';
import { Youtube, Music, Video, Speaker } from '@/components/icons';
import { type SharedData } from '@/types';

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

interface Props {
    downloads?: Download[];
    [key: string]: unknown;
}

export default function Welcome({ downloads = [] }: Props) {
    const { auth } = usePage<SharedData>().props;

    return (
        <>
            <Head title="YouTube Video Downloader">
                <link rel="preconnect" href="https://fonts.bunny.net" />
                <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
            </Head>
            <div className="min-h-screen bg-gray-50">
                {/* Header */}
                <header className="bg-white shadow-sm border-b">
                    <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                        <div className="flex justify-between items-center py-4">
                            <div className="flex items-center gap-3">
                                <Youtube className="h-8 w-8 text-red-500" />
                                <h1 className="text-xl font-bold text-gray-900">YouTube Downloader</h1>
                            </div>
                            <nav className="flex items-center gap-4">
                                {auth.user ? (
                                    <Link
                                        href={route('dashboard')}
                                        className="inline-block rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
                                    >
                                        Dashboard
                                    </Link>
                                ) : (
                                    <>
                                        <Link
                                            href={route('login')}
                                            className="text-sm font-medium text-gray-700 hover:text-blue-600"
                                        >
                                            Log in
                                        </Link>
                                        <Link
                                            href={route('register')}
                                            className="inline-block rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700"
                                        >
                                            Register
                                        </Link>
                                    </>
                                )}
                            </nav>
                        </div>
                    </div>
                </header>

                {/* Hero Section */}
                <div className="bg-gradient-to-br from-blue-50 to-indigo-100 py-16">
                    <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                        <div className="text-center mb-12">
                            <h2 className="text-4xl font-bold text-gray-900 mb-4">
                                🎵 YouTube Video Downloader & Converter
                            </h2>
                            <p className="text-xl text-gray-600 max-w-3xl mx-auto">
                                Download and convert your favorite YouTube videos to MP3, MP4, or WAV format. 
                                Fast, reliable, and supports multiple simultaneous downloads.
                            </p>
                        </div>

                        {/* Feature highlights */}
                        <div className="grid grid-cols-1 md:grid-cols-3 gap-8 mb-12">
                            <div className="text-center">
                                <div className="bg-white rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4 shadow-md">
                                    <Music className="h-8 w-8 text-green-500" />
                                </div>
                                <h3 className="text-lg font-semibold mb-2">MP3 Audio</h3>
                                <p className="text-gray-600">High-quality audio extraction for music and podcasts</p>
                            </div>
                            <div className="text-center">
                                <div className="bg-white rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4 shadow-md">
                                    <Video className="h-8 w-8 text-blue-500" />
                                </div>
                                <h3 className="text-lg font-semibold mb-2">MP4 Video</h3>
                                <p className="text-gray-600">Full video downloads with original quality</p>
                            </div>
                            <div className="text-center">
                                <div className="bg-white rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4 shadow-md">
                                    <Speaker className="h-8 w-8 text-purple-500" />
                                </div>
                                <h3 className="text-lg font-semibold mb-2">WAV Format</h3>
                                <p className="text-gray-600">Uncompressed audio for professional use</p>
                            </div>
                        </div>

                        {/* Download Form */}
                        <VideoDownloadForm />
                    </div>
                </div>

                {/* Downloads Section */}
                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
                    <DownloadsList downloads={downloads} />
                </div>

                {/* Footer */}
                <footer className="bg-white border-t mt-16">
                    <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                        <div className="text-center text-sm text-gray-500">
                            Built with ❤️ by{" "}
                            <a 
                                href="https://app.build" 
                                target="_blank" 
                                className="font-medium text-blue-600 hover:underline"
                            >
                                app.build
                            </a>
                        </div>
                    </div>
                </footer>
            </div>
        </>
    );
}