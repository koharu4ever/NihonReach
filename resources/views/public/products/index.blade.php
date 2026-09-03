@extends('layouts.public')

@section('title', __('製品情報'))
@section('meta_description', __('NihonReach Portfolio Demo の架空の精密切削工具一覧です。カテゴリ別に製品と仕様を確認できます。'))

@section('content')
    <section class="metal-sheen border-b border-slate-300">
        <div class="mx-auto max-w-[1180px] px-5 py-14 lg:px-0 lg:py-16">
            <nav class="flex items-center gap-2 text-xs font-medium text-slate-500" aria-label="{{ __('パンくず') }}">
                <a href="{{ \App\Support\PublicSite::route('home') }}" class="hover:text-red-800">{{ __('ホーム') }}</a>
                <span aria-hidden="true">/</span>
                <span aria-current="page" class="text-slate-800">{{ __('製品情報') }}</span>
            </nav>
            <div class="mt-8 grid gap-6 lg:grid-cols-[.7fr_1.3fr] lg:items-end">
                <div>
                    <p class="text-xs font-bold uppercase tracking-[0.18em] text-red-700">{{ __('Product catalog') }}</p>
                    <h1 class="mt-3 text-4xl font-bold tracking-[-0.04em] text-slate-950 sm:text-5xl">{{ __('製品情報') }}</h1>
                </div>
                <p class="max-w-2xl text-sm leading-7 text-slate-600 lg:justify-self-end">
                    {{ __(':count つのカテゴリから架空の精密切削工具を選び、製品ごとの説明と可変仕様を確認できます。', ['count' => $categories->count()]) }}
                </p>
            </div>
        </div>
    </section>

    <div class="mx-auto max-w-[1180px] px-5 py-12 lg:px-0 lg:py-16">
        <div class="border-b border-slate-300 pb-8">
            <p class="mb-4 text-xs font-bold uppercase tracking-[0.16em] text-slate-500">{{ __('Filter by category') }}</p>
            <nav class="-mx-5 flex gap-2 overflow-x-auto px-5 pb-2 lg:mx-0 lg:flex-wrap lg:px-0" aria-label="{{ __('製品カテゴリ') }}">
                <a
                    href="{{ \App\Support\PublicSite::route('products.index') }}"
                    @if ($selectedCategory === '') aria-current="page" @endif
                    @class([
                        'inline-flex min-h-11 shrink-0 items-center border px-4 text-sm font-bold transition',
                        'border-slate-950 bg-slate-950 text-white' => $selectedCategory === '',
                        'border-slate-300 bg-white text-slate-700 hover:border-red-700 hover:text-red-800' => $selectedCategory !== '',
                    ])
                >
                    {{ __('すべて') }} <span class="ml-2 text-xs opacity-70">{{ $categories->sum('active_products_count') }}</span>
                </a>
                @foreach ($categories as $category)
                    <a
                        href="{{ \App\Support\PublicSite::route('products.index', ['category' => $category->slug]) }}"
                        @if ($selectedCategory === $category->slug) aria-current="page" @endif
                        @class([
                            'inline-flex min-h-11 shrink-0 items-center border px-4 text-sm font-bold transition',
                            'border-red-800 bg-red-700 text-white' => $selectedCategory === $category->slug,
                            'border-slate-300 bg-white text-slate-700 hover:border-red-700 hover:text-red-800' => $selectedCategory !== $category->slug,
                        ])
                    >
                        {{ $category->translated('name') }}
                        <span class="ml-2 text-xs opacity-70">{{ $category->active_products_count }}</span>
                    </a>
                @endforeach
            </nav>
        </div>

        <div class="mt-8 flex items-center justify-between">
            <p class="text-sm text-slate-600">
                <strong class="text-lg text-slate-950">{{ $products->total() }}</strong>
                <span class="ml-1">{{ __('件の Demo 製品') }}</span>
            </p>
            <p class="hidden text-xs font-medium text-slate-500 sm:block">{{ __('公開中の製品とカテゴリのみ表示') }}</p>
        </div>

        <div class="mt-7 grid gap-6 md:grid-cols-2 lg:grid-cols-3">
            @forelse ($products as $product)
                <x-public.product-card :product="$product" />
            @empty
                <div class="col-span-full border border-dashed border-slate-400 bg-white px-6 py-16 text-center">
                    <p class="font-bold text-slate-900">{{ __('該当する製品はありません。') }}</p>
                    <p class="mt-2 text-sm text-slate-500">{{ __('別のカテゴリを選択してください。') }}</p>
                    <a href="{{ \App\Support\PublicSite::route('products.index') }}" class="mt-6 inline-flex min-h-11 items-center border-b border-red-700 text-sm font-bold text-red-800">
                        {{ __('すべての製品に戻る') }}
                    </a>
                </div>
            @endforelse
        </div>

        @if ($products->hasPages())
            <div class="mt-12 border-t border-slate-200 pt-8">
                {{ $products->links() }}
            </div>
        @endif
    </div>
@endsection
