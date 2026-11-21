<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('title', config('app.name', 'Sistem Pencatatan Stunting'))</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts & Styles -->
        {{-- Pakai Vite bila aset sudah di-build (npm run build) atau dev server aktif (npm run dev).
             Jika belum, jatuh ke CDN agar halaman tidak error "Vite manifest not found". --}}
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @else
            <script src="https://cdn.tailwindcss.com"></script>
            <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
            <style>[x-cloak]{display:none !important;}</style>
        @endif

        @stack('styles')
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
            @include('layouts.navigation')

            <!-- Page Heading -->
            {{-- Mendukung <x-slot name="header"> maupun @section('header') --}}
            @if (isset($header))
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @elseif (View::hasSection('header'))
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        @yield('header')
                    </div>
                </header>
            @endif

            <!-- Page Content -->
            {{-- $slot  : saat dipakai sebagai komponen  -> <x-app-layout> ... </x-app-layout>
                 content: saat dipakai lewat @extends('layouts.app') + @section('content')
                 Hanya salah satu yang terisi, jadi aman ditulis berurutan. --}}
            <main>
                {{ $slot ?? '' }}
                @yield('content')
            </main>
        </div>

        @stack('scripts')
    </body>
</html>