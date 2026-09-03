@php
    $currentLocale = app()->getLocale();
    $isChinese = $currentLocale === 'zh';
    $pageTitle = trim($__env->yieldContent('title', __('精密切削工具カタログ')));
    $pageDescription = trim($__env->yieldContent(
        'meta_description',
        __('NihonReach は精密切削工具の B2B サイトを想定したポートフォリオデモです。'),
    ));
    $openGraphImage = trim($__env->yieldContent('og_image', asset('images/site/og-cover.webp')));
    $openGraphWidth = trim($__env->yieldContent('og_width', '1200'));
    $openGraphHeight = trim($__env->yieldContent('og_height', '630'));
    $japaneseUrl = \App\Support\PublicSite::switchUrl('ja');
    $chineseUrl = \App\Support\PublicSite::switchUrl('zh');
@endphp

<!DOCTYPE html>
<html lang="{{ $isChinese ? 'zh-CN' : 'ja' }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="{{ $pageDescription }}">
        <meta name="theme-color" content="#111827">

        <title>{{ $pageTitle }} | NihonReach</title>

        <link rel="canonical" href="{{ url()->current() }}">
        <link rel="alternate" hreflang="ja" href="{{ $japaneseUrl }}">
        <link rel="alternate" hreflang="zh-CN" href="{{ $chineseUrl }}">
        <link rel="alternate" hreflang="x-default" href="{{ $japaneseUrl }}">
        <link rel="icon" href="/favicon.ico" sizes="any">
        <link rel="icon" href="/favicon.svg" type="image/svg+xml">
        <link rel="apple-touch-icon" href="/apple-touch-icon.png">

        <meta property="og:locale" content="{{ $isChinese ? 'zh_CN' : 'ja_JP' }}">
        <meta property="og:locale:alternate" content="{{ $isChinese ? 'ja_JP' : 'zh_CN' }}">
        <meta property="og:type" content="website">
        <meta property="og:site_name" content="NihonReach Portfolio Demo">
        <meta property="og:title" content="{{ $pageTitle }} | NihonReach">
        <meta property="og:description" content="{{ $pageDescription }}">
        <meta property="og:url" content="{{ url()->current() }}">
        <meta property="og:image" content="{{ $openGraphImage }}">
        <meta property="og:image:width" content="{{ $openGraphWidth }}">
        <meta property="og:image:height" content="{{ $openGraphHeight }}">

        @fonts
        @vite('resources/css/app.css')
        @stack('head')
    </head>
    <body class="public-site min-h-screen antialiased">
        <a href="#main-content" class="fixed left-4 top-3 z-[100] -translate-y-24 bg-white px-4 py-3 text-sm font-bold text-slate-950 shadow-lg transition focus:translate-y-0">
            {{ __('本文へ移動') }}
        </a>

        <x-public.demo-notice />

        <header class="sticky top-0 z-50 border-b border-slate-200 bg-white/95 backdrop-blur-md">
            <div class="mx-auto flex min-h-[76px] max-w-[1180px] items-center justify-between gap-6 px-5 lg:px-0">
                <a href="{{ \App\Support\PublicSite::route('home') }}" class="shrink-0" aria-label="{{ __('NihonReach ホーム') }}">
                    <x-public.brand />
                </a>

                <nav class="hidden items-stretch gap-7 self-stretch lg:flex" aria-label="{{ __('メインナビゲーション') }}">
                    <x-public.nav-link :href="\App\Support\PublicSite::route('home')" :active="request()->routeIs('home', 'zh.home')">
                        {{ __('ホーム') }}
                    </x-public.nav-link>
                    <x-public.nav-link :href="\App\Support\PublicSite::route('products.index')" :active="request()->routeIs('products.*', 'zh.products.*')">
                        {{ __('製品情報') }}
                    </x-public.nav-link>
                    <x-public.nav-link :href="\App\Support\PublicSite::route('about')" :active="request()->routeIs('about', 'zh.about')">
                        {{ __('制作概要') }}
                    </x-public.nav-link>
                </nav>

                <div class="hidden items-center gap-3 lg:flex">
                    @auth
                        @if (auth()->user()->is_admin)
                            <a href="{{ route('dashboard') }}" class="inline-flex min-h-11 items-center px-3 text-xs font-bold text-slate-600 transition hover:text-slate-950">
                                {{ __('管理画面') }}
                            </a>
                        @endif
                    @else
                        <a href="{{ route('login') }}" class="inline-flex min-h-11 items-center px-3 text-xs font-bold text-slate-600 transition hover:text-slate-950">
                            {{ __('管理者ログイン') }}
                        </a>
                    @endauth
                    <div class="inline-flex border border-slate-300 bg-white p-0.5 text-xs font-bold" aria-label="{{ __('言語選択') }}">
                        <a href="{{ $japaneseUrl }}" lang="ja" hreflang="ja" @if (! $isChinese) aria-current="page" @endif @class([
                            'px-2.5 py-2 transition',
                            'bg-slate-950 text-white' => ! $isChinese,
                            'text-slate-600 hover:text-slate-950' => $isChinese,
                        ])>日本語</a>
                        <a href="{{ $chineseUrl }}" lang="zh-CN" hreflang="zh-CN" @if ($isChinese) aria-current="page" @endif @class([
                            'px-2.5 py-2 transition',
                            'bg-slate-950 text-white' => $isChinese,
                            'text-slate-600 hover:text-slate-950' => ! $isChinese,
                        ])>中文</a>
                    </div>
                    <a href="{{ \App\Support\PublicSite::route('inquiries.create') }}" @class([
                        'inline-flex min-h-11 items-center justify-center bg-red-700 px-5 text-sm font-bold text-white transition hover:bg-red-800',
                        'ring-2 ring-red-900 ring-offset-2' => request()->routeIs('inquiries.*', 'zh.inquiries.*'),
                    ])>
                        {{ __('お問い合わせ') }}
                        <span class="ml-2" aria-hidden="true">→</span>
                    </a>
                </div>

                <details class="group relative lg:hidden">
                    <summary class="flex size-11 cursor-pointer list-none items-center justify-center border border-slate-300 bg-white text-slate-900" aria-label="{{ __('メニュー') }}">
                        <svg class="size-5 group-open:hidden" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                        <svg class="hidden size-5 group-open:block" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path d="m6 6 12 12M18 6 6 18"/>
                        </svg>
                    </summary>
                    <div class="absolute right-0 top-[calc(100%+1rem)] w-[min(21rem,calc(100vw-2.5rem))] border border-slate-200 bg-white p-3 shadow-2xl">
                        <nav class="grid" aria-label="{{ __('モバイルナビゲーション') }}">
                            <a href="{{ \App\Support\PublicSite::route('home') }}" @if (request()->routeIs('home', 'zh.home')) aria-current="page" @endif class="border-b border-slate-100 px-4 py-3 text-sm font-bold text-slate-800 hover:bg-slate-50">{{ __('ホーム') }}</a>
                            <a href="{{ \App\Support\PublicSite::route('products.index') }}" @if (request()->routeIs('products.*', 'zh.products.*')) aria-current="page" @endif class="border-b border-slate-100 px-4 py-3 text-sm font-bold text-slate-800 hover:bg-slate-50">{{ __('製品情報') }}</a>
                            <a href="{{ \App\Support\PublicSite::route('about') }}" @if (request()->routeIs('about', 'zh.about')) aria-current="page" @endif class="border-b border-slate-100 px-4 py-3 text-sm font-bold text-slate-800 hover:bg-slate-50">{{ __('制作概要') }}</a>
                            <a href="{{ \App\Support\PublicSite::route('inquiries.create') }}" @if (request()->routeIs('inquiries.*', 'zh.inquiries.*')) aria-current="page" @endif class="mt-3 bg-red-700 px-4 py-3 text-center text-sm font-bold text-white hover:bg-red-800">{{ __('お問い合わせ') }}</a>
                            <div class="mt-2 grid grid-cols-2 gap-2" aria-label="{{ __('言語選択') }}">
                                <a href="{{ $japaneseUrl }}" lang="ja" hreflang="ja" @if (! $isChinese) aria-current="page" @endif @class(['border px-3 py-2 text-center text-xs font-bold', 'border-slate-950 bg-slate-950 text-white' => ! $isChinese, 'border-slate-300 text-slate-600' => $isChinese])>日本語</a>
                                <a href="{{ $chineseUrl }}" lang="zh-CN" hreflang="zh-CN" @if ($isChinese) aria-current="page" @endif @class(['border px-3 py-2 text-center text-xs font-bold', 'border-slate-950 bg-slate-950 text-white' => $isChinese, 'border-slate-300 text-slate-600' => ! $isChinese])>中文</a>
                            </div>
                            @auth
                                @if (auth()->user()->is_admin)
                                    <a href="{{ route('dashboard') }}" class="mt-2 px-4 py-3 text-center text-xs font-bold text-slate-600 hover:bg-slate-50">{{ __('管理画面') }}</a>
                                @endif
                            @else
                                <a href="{{ route('login') }}" class="mt-2 px-4 py-3 text-center text-xs font-bold text-slate-600 hover:bg-slate-50">{{ __('管理者ログイン') }}</a>
                            @endauth
                        </nav>
                    </div>
                </details>
            </div>
        </header>

        <main id="main-content">
            @yield('content')
        </main>

        <footer class="mt-24 bg-slate-950 text-slate-300">
            <div class="border-b border-white/10">
                <div class="mx-auto flex max-w-[1180px] flex-col gap-6 px-5 py-10 sm:flex-row sm:items-center sm:justify-between lg:px-0">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-[0.18em] text-red-400">{{ __('Catalog to inquiry') }}</p>
                        <h2 class="mt-2 text-2xl font-bold tracking-tight text-white">{{ __('製品を選び、問い合わせ管理まで試す。') }}</h2>
                    </div>
                    <a href="{{ \App\Support\PublicSite::route('inquiries.create') }}" class="inline-flex min-h-12 items-center justify-center bg-red-700 px-6 text-sm font-bold text-white transition hover:bg-red-600">
                        {{ __('Demo お問い合わせ') }}
                        <span class="ml-3" aria-hidden="true">→</span>
                    </a>
                </div>
            </div>

            <div class="mx-auto grid max-w-[1180px] gap-10 px-5 py-14 md:grid-cols-[1.25fr_.75fr_.75fr] lg:px-0">
                <div>
                    <x-public.brand inverse />
                    <p class="mt-5 max-w-md text-sm leading-7 text-slate-400">
                        {{ __('Laravel・Blade・Inertia・Vue を一つの業務風アプリケーションとして構成した、求職用 B2B Portfolio Demo です。') }}
                    </p>
                </div>
                <div>
                    <p class="text-xs font-bold uppercase tracking-[0.16em] text-white">{{ __('Site map') }}</p>
                    <div class="mt-4 grid gap-3 text-sm text-slate-400">
                        <a href="{{ \App\Support\PublicSite::route('products.index') }}" class="hover:text-white">{{ __('製品情報') }}</a>
                        <a href="{{ \App\Support\PublicSite::route('inquiries.create') }}" class="hover:text-white">{{ __('お問い合わせ') }}</a>
                        <a href="{{ \App\Support\PublicSite::route('about') }}" class="hover:text-white">{{ __('制作概要') }}</a>
                    </div>
                </div>
                <div>
                    <p class="text-xs font-bold uppercase tracking-[0.16em] text-white">{{ __('Scope') }}</p>
                    <p class="mt-4 text-sm leading-7 text-slate-400">
                        {{ __('公開カタログ、管理 CRUD、認証、問い合わせ状態管理、テストを含む Laravel 単体アプリケーション。') }}
                    </p>
                </div>
            </div>
            <div class="border-t border-white/10 px-5 py-5 text-center text-xs leading-5 text-slate-500">
                © {{ now()->year }} NihonReach · Portfolio Demo · {{ __('実在企業・実在製品とは関係ありません') }}
            </div>
        </footer>
    </body>
</html>
