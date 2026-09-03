@extends('layouts.public')

@section('title', __('精密切削工具の B2B カタログ'))
@section('meta_description', __('カテゴリ別の精密切削工具カタログ、製品仕様、問い合わせ管理までを実装した NihonReach Portfolio Demo です。'))

@section('content')
    <section class="relative isolate overflow-hidden bg-slate-950 text-white">
        <img
            src="{{ asset('images/site/home-hero-precision-tools.webp') }}"
            alt=""
            width="1535"
            height="863"
            decoding="async"
            fetchpriority="high"
            class="absolute inset-0 -z-20 size-full object-cover object-[68%_center]"
        >
        <div class="absolute inset-0 -z-10 bg-gradient-to-r from-slate-950 via-slate-950/95 to-slate-950/5"></div>
        <div class="absolute inset-0 -z-10 bg-gradient-to-t from-slate-950/80 via-transparent to-slate-950/20"></div>

        <div class="mx-auto flex min-h-[590px] max-w-[1180px] items-center px-5 py-20 lg:px-0">
            <div class="max-w-[680px]">
                <p class="inline-flex border-l-2 border-red-500 pl-3 text-xs font-bold uppercase tracking-[0.2em] text-slate-200">
                    {{ __('Precision tool catalog · Portfolio Demo') }}
                </p>
                <h1 class="mt-7 text-4xl font-bold leading-[1.15] tracking-[-0.045em] sm:text-5xl lg:text-[3.75rem]">
                    {{ __('カテゴリから選ぶ、') }}<br>
                    {{ __('精密切削工具のデモカタログ。') }}
                </h1>
                <p class="mt-7 max-w-2xl text-base leading-8 text-slate-300 sm:text-lg">
                    {{ __('製品分類、仕様確認、問い合わせ登録、管理画面での対応状況更新まで。B2B 製品サイトの基本導線を一つの Laravel アプリケーションで構成しています。') }}
                </p>
                <div class="mt-9 flex flex-col gap-3 sm:flex-row">
                    <a href="{{ \App\Support\PublicSite::route('products.index') }}" class="inline-flex min-h-12 items-center justify-center bg-red-700 px-6 text-sm font-bold text-white transition hover:bg-red-600">
                        {{ __('製品カタログを見る') }}
                        <span class="ml-3" aria-hidden="true">→</span>
                    </a>
                    <a href="{{ \App\Support\PublicSite::route('about') }}" class="inline-flex min-h-12 items-center justify-center border border-white/40 bg-black/10 px-6 text-sm font-bold text-white transition hover:border-white hover:bg-white/10">
                        {{ __('実装内容を確認') }}
                    </a>
                </div>
            </div>
        </div>
    </section>

    <section class="border-b border-slate-200 bg-white" aria-label="{{ __('カタログ概要') }}">
        <div class="mx-auto grid max-w-[1180px] divide-y divide-slate-200 px-5 sm:grid-cols-3 sm:divide-x sm:divide-y-0 lg:px-0">
            <div class="flex items-center gap-4 py-6 sm:px-6 sm:first:pl-0">
                <strong class="text-3xl font-bold tabular-nums text-red-700">{{ $categories->count() }}</strong>
                <span class="text-xs font-bold leading-5 text-slate-600">{{ __('公開') }}<br>{{ __('カテゴリ') }}</span>
            </div>
            <div class="flex items-center gap-4 py-6 sm:px-6">
                <strong class="text-3xl font-bold tabular-nums text-red-700">{{ $categories->sum('active_products_count') }}</strong>
                <span class="text-xs font-bold leading-5 text-slate-600">{{ __('オリジナル') }}<br>{{ __('Demo 製品') }}</span>
            </div>
            <div class="flex items-center gap-4 py-6 sm:px-6">
                <strong class="text-3xl font-bold text-red-700">3</strong>
                <span class="text-xs font-bold leading-5 text-slate-600">{{ __('閲覧・送信・管理の') }}<br>{{ __('基本フロー') }}</span>
            </div>
        </div>
    </section>

    <section class="mx-auto max-w-[1180px] px-5 py-20 lg:px-0 lg:py-24">
        <div class="flex flex-col gap-5 md:flex-row md:items-end md:justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.18em] text-red-700">{{ __('Tool categories') }}</p>
                <h2 class="mt-3 text-3xl font-bold tracking-[-0.035em] text-slate-950 sm:text-4xl">{{ __('製品カテゴリから探す') }}</h2>
                <p class="mt-4 max-w-2xl text-sm leading-7 text-slate-600">
                    {{ __(':count つの加工用途を入口に、公開中の製品と仕様へ移動できます。', ['count' => $categories->count()]) }}
                </p>
            </div>
            <a href="{{ \App\Support\PublicSite::route('products.index') }}" class="inline-flex min-h-11 items-center text-sm font-bold text-slate-950 hover:text-red-800">
                {{ __('すべての製品を見る') }} <span class="ml-2 text-red-700" aria-hidden="true">→</span>
            </a>
        </div>

        @php
            $categoryImages = [
                'solid-carbide-end-mills' => 'images/products/nr-demo-4-flute-end-mill-6mm.webp',
                'indexable-milling-tools' => 'images/products/nr-demo-shoulder-cutter-50mm.webp',
                'drilling-tools' => 'images/products/nr-demo-carbide-drill-8mm.webp',
                'turning-tools' => 'images/products/nr-demo-external-turning-holder.webp',
            ];
        @endphp

        <div class="mt-10 grid grid-cols-2 gap-3 sm:gap-5 lg:grid-cols-4">
            @foreach ($categories as $category)
                <a href="{{ \App\Support\PublicSite::route('products.index', ['category' => $category->slug]) }}" class="group flex h-full flex-col border border-slate-200 bg-white transition hover:border-slate-400 hover:shadow-[0_18px_50px_-34px_rgba(15,23,42,.7)]">
                    <div class="relative overflow-hidden bg-slate-100">
                        <img
                            src="{{ asset($categoryImages[$category->slug] ?? 'images/site/og-cover.webp') }}"
                            alt="{{ __(':category の代表製品イメージ', ['category' => $category->translated('name')]) }}"
                            width="1200"
                            height="900"
                            loading="lazy"
                            decoding="async"
                            class="aspect-[4/3] w-full object-cover transition duration-500 group-hover:scale-[1.025]"
                        >
                        <span class="absolute left-0 top-0 bg-slate-950 px-3 py-2 text-[10px] font-bold tracking-[0.15em] text-white">
                            {{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}
                        </span>
                    </div>
                    <div class="flex flex-1 flex-col p-4 sm:p-5">
                        <p class="text-[10px] font-bold uppercase tracking-[0.14em] text-red-700">
                            {{ __(':count products', ['count' => $category->active_products_count]) }}
                        </p>
                        <h3 class="mt-2 text-sm font-bold leading-6 text-slate-950 sm:text-base">{{ $category->translated('name') }}</h3>
                        <p class="mt-2 hidden flex-1 text-sm leading-6 text-slate-600 sm:block">
                            {{ $category->translated('description') ?: __('公開製品をまとめた Demo カテゴリです。') }}
                        </p>
                        <span class="mt-4 inline-flex items-center text-xs font-bold text-slate-800 group-hover:text-red-800">
                            {{ __('カテゴリを見る') }} <span class="ml-2 text-red-700" aria-hidden="true">→</span>
                        </span>
                    </div>
                </a>
            @endforeach
        </div>
    </section>

    <section class="border-y border-slate-200 bg-[#eceeec]">
        <div class="mx-auto max-w-[1180px] px-5 py-20 lg:px-0 lg:py-24">
            <div class="max-w-2xl">
                <p class="text-xs font-bold uppercase tracking-[0.18em] text-red-700">{{ __('Featured products') }}</p>
                <h2 class="mt-3 text-3xl font-bold tracking-[-0.035em] text-slate-950 sm:text-4xl">{{ __('注目の Demo 製品') }}</h2>
                <p class="mt-4 text-sm leading-7 text-slate-600">
                    {{ __('管理画面で「おすすめ」として設定された、公開中の製品を表示しています。') }}
                </p>
            </div>

            <div class="mt-10 grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                @forelse ($featuredProducts as $product)
                    <x-public.product-card :product="$product" />
                @empty
                    <p class="col-span-full border border-dashed border-slate-400 bg-white px-6 py-14 text-center text-sm text-slate-600">
                        {{ __('おすすめ製品はまだ登録されていません。') }}
                    </p>
                @endforelse
            </div>
        </div>
    </section>

    <section class="bg-slate-950 text-white">
        <div class="mx-auto grid max-w-[1180px] gap-12 px-5 py-20 lg:grid-cols-[.85fr_1.15fr] lg:px-0 lg:py-24">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.18em] text-red-400">{{ __('Application flow') }}</p>
                <h2 class="mt-4 text-3xl font-bold leading-tight tracking-[-0.035em] sm:text-4xl">
                    {{ __('公開カタログから、') }}<br>{{ __('管理業務までつなぐ。') }}
                </h2>
                <p class="mt-5 max-w-lg text-sm leading-7 text-slate-400">
                    {{ __('表示だけのランディングページではなく、訪問者の入力が MySQL に保存され、管理者が対応状況を更新できる業務フローを実装しています。') }}
                </p>
                <a href="{{ \App\Support\PublicSite::route('about') }}" class="mt-8 inline-flex min-h-11 items-center border-b border-red-500 text-sm font-bold text-white hover:text-red-200">
                    {{ __('アーキテクチャを見る') }} <span class="ml-3" aria-hidden="true">→</span>
                </a>
            </div>

            <ol class="grid border border-white/15 sm:grid-cols-3">
                <li class="border-b border-white/15 p-6 sm:border-b-0 sm:border-r">
                    <span class="font-mono text-xs font-bold text-red-400">{{ __('01 / DISCOVER') }}</span>
                    <h3 class="mt-6 text-lg font-bold">{{ __('製品を探す') }}</h3>
                    <p class="mt-3 text-sm leading-7 text-slate-400">{{ __('カテゴリ、製品詳細、可変仕様から対象を確認します。') }}</p>
                </li>
                <li class="border-b border-white/15 p-6 sm:border-b-0 sm:border-r">
                    <span class="font-mono text-xs font-bold text-red-400">{{ __('02 / INQUIRE') }}</span>
                    <h3 class="mt-6 text-lg font-bold">{{ __('相談を登録') }}</h3>
                    <p class="mt-3 text-sm leading-7 text-slate-400">{{ __('製品を引き継ぎ、検証済みフォームから問い合わせを保存します。') }}</p>
                </li>
                <li class="p-6">
                    <span class="font-mono text-xs font-bold text-red-400">{{ __('03 / MANAGE') }}</span>
                    <h3 class="mt-6 text-lg font-bold">{{ __('状態を管理') }}</h3>
                    <p class="mt-3 text-sm leading-7 text-slate-400">{{ __('管理者が new・in progress・closed を更新します。') }}</p>
                </li>
            </ol>
        </div>
    </section>

    <section class="mx-auto max-w-[1180px] px-5 py-20 lg:px-0 lg:py-24">
        <div class="grid gap-px border border-slate-200 bg-slate-200 md:grid-cols-2 lg:grid-cols-4">
            <article class="bg-white p-6">
                <span class="text-xs font-bold text-red-700">01</span>
                <h2 class="mt-5 font-bold text-slate-950">{{ __('Blade 公開サイト') }}</h2>
                <p class="mt-3 text-sm leading-7 text-slate-600">{{ __('閲覧中心のページをサーバーで描画し、製品情報を明確に提示。') }}</p>
            </article>
            <article class="bg-white p-6">
                <span class="text-xs font-bold text-red-700">02</span>
                <h2 class="mt-5 font-bold text-slate-950">{{ __('Inertia / Vue 管理画面') }}</h2>
                <p class="mt-3 text-sm leading-7 text-slate-600">{{ __('独立 API を増やさず、Laravel の認証と CRUD をコンポーネント化。') }}</p>
            </article>
            <article class="bg-white p-6">
                <span class="text-xs font-bold text-red-700">03</span>
                <h2 class="mt-5 font-bold text-slate-950">{{ __('サーバー側の業務ルール') }}</h2>
                <p class="mt-3 text-sm leading-7 text-slate-600">{{ __('公開条件、入力検証、管理者権限、問い合わせ状態をバックエンドで保証。') }}</p>
            </article>
            <article class="bg-white p-6">
                <span class="text-xs font-bold text-red-700">04</span>
                <h2 class="mt-5 font-bold text-slate-950">{{ __('自動化された品質確認') }}</h2>
                <p class="mt-3 text-sm leading-7 text-slate-600">{{ __('PHPUnit、PHPStan、Pint、TypeScript と本番ビルドで検証。') }}</p>
            </article>
        </div>
    </section>
@endsection
