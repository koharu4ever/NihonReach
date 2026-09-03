@extends('layouts.public')

@section('title', $product->translated('name'))
@section('meta_description', $product->translated('summary'))
@section('og_image', $product->image_path ? asset(ltrim($product->image_path, '/')) : asset('images/site/og-cover.webp'))
@section('og_width', '1200')
@section('og_height', $product->image_path ? '900' : '630')

@php
    $specifications = $product->translated('specifications') ?? [];
@endphp

@section('content')
    <div class="mx-auto max-w-[1180px] px-5 py-8 lg:px-0 lg:py-10">
        <nav class="flex flex-wrap items-center gap-2 text-xs font-medium text-slate-500" aria-label="{{ __('パンくず') }}">
            <a href="{{ \App\Support\PublicSite::route('home') }}" class="hover:text-red-800">{{ __('ホーム') }}</a>
            <span aria-hidden="true">/</span>
            <a href="{{ \App\Support\PublicSite::route('products.index') }}" class="hover:text-red-800">{{ __('製品情報') }}</a>
            <span aria-hidden="true">/</span>
            <a href="{{ \App\Support\PublicSite::route('products.index', ['category' => $product->category->slug]) }}" class="hover:text-red-800">
                {{ $product->category->translated('name') }}
            </a>
            <span aria-hidden="true">/</span>
            <span aria-current="page" class="max-w-full truncate text-slate-800">{{ $product->translated('name') }}</span>
        </nav>

        <div class="mt-8 grid gap-10 lg:grid-cols-[1.05fr_.95fr] lg:items-start">
            <x-public.product-media :product="$product" eager variant="detail" class="border border-slate-200" />

            <div class="lg:pt-3">
                <a href="{{ \App\Support\PublicSite::route('products.index', ['category' => $product->category->slug]) }}" class="text-xs font-bold uppercase tracking-[0.14em] text-red-700 hover:text-red-900">
                    {{ $product->category->translated('name') }}
                </a>
                <h1 class="mt-4 text-3xl font-bold leading-[1.25] tracking-[-0.04em] text-slate-950 sm:text-4xl">
                    {{ $product->translated('name') }}
                </h1>
                <p class="mt-4 font-mono text-sm font-semibold tracking-wide text-slate-500">{{ __('MODEL') }} / {{ $product->sku }}</p>
                <p class="mt-6 text-base leading-8 text-slate-700">{{ $product->translated('summary') }}</p>

                @if (filled($specifications))
                    <dl class="mt-7 grid grid-cols-2 gap-px border border-slate-200 bg-slate-200 sm:grid-cols-3">
                        @foreach (collect($specifications)->take(3) as $specification)
                            <div class="bg-white px-4 py-4">
                                <dt class="text-[10px] font-bold tracking-wide text-slate-500">{{ $specification['label'] }}</dt>
                                <dd class="mt-1.5 text-sm font-bold text-slate-950">{{ $specification['value'] }}</dd>
                            </div>
                        @endforeach
                    </dl>
                @endif

                <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                    <a href="{{ \App\Support\PublicSite::route('inquiries.create', ['product' => $product->slug]) }}" class="inline-flex min-h-12 flex-1 items-center justify-center bg-red-700 px-5 text-sm font-bold text-white transition hover:bg-red-800">
                        {{ __('この製品について相談する') }}
                        <span class="ml-3" aria-hidden="true">→</span>
                    </a>
                    <a href="#specifications" class="inline-flex min-h-12 items-center justify-center border border-slate-400 bg-white px-5 text-sm font-bold text-slate-800 transition hover:border-slate-950">
                        {{ __('仕様を確認') }}
                    </a>
                </div>

                <div class="mt-6 border-l-2 border-amber-500 bg-amber-50 px-4 py-3 text-xs leading-6 text-amber-950">
                    {{ __('製品名・型番・仕様は Portfolio Demo 用の架空データです。実際の加工性能を保証するものではありません。') }}
                </div>
            </div>
        </div>

        <div class="mt-16 grid gap-12 border-t border-slate-300 pt-14 lg:grid-cols-[1.05fr_.95fr]">
            <section>
                <p class="text-xs font-bold uppercase tracking-[0.18em] text-red-700">{{ __('Product overview') }}</p>
                <h2 class="mt-3 text-2xl font-bold tracking-[-0.025em] text-slate-950">{{ __('製品説明') }}</h2>
                <div class="mt-6 whitespace-pre-line text-sm leading-8 text-slate-700">{{ $product->translated('description') }}</div>

                <div class="mt-9 grid gap-px border border-slate-200 bg-slate-200 sm:grid-cols-2">
                    <div class="bg-white p-5">
                        <p class="text-[10px] font-bold uppercase tracking-[0.15em] text-slate-500">{{ __('Visibility rule') }}</p>
                        <p class="mt-2 text-sm leading-6 text-slate-700">{{ __('製品と所属カテゴリの両方が公開中の場合のみ、このページを表示します。') }}</p>
                    </div>
                    <div class="bg-white p-5">
                        <p class="text-[10px] font-bold uppercase tracking-[0.15em] text-slate-500">{{ __('Inquiry flow') }}</p>
                        <p class="mt-2 text-sm leading-6 text-slate-700">{{ __('問い合わせ画面へ製品情報を引き継ぎ、サーバー側で再検証します。') }}</p>
                    </div>
                </div>
            </section>

            <section id="specifications" class="scroll-mt-28">
                <p class="text-xs font-bold uppercase tracking-[0.18em] text-red-700">{{ __('Specifications') }}</p>
                <h2 class="mt-3 text-2xl font-bold tracking-[-0.025em] text-slate-950">{{ __('主な仕様') }}</h2>
                <dl class="mt-6 border border-slate-300 bg-white">
                    @forelse ($specifications as $specification)
                        <div class="grid border-b border-slate-200 last:border-0 sm:grid-cols-[minmax(9rem,.8fr)_1.2fr]">
                            <dt class="bg-slate-100 px-4 py-3 text-sm font-bold text-slate-600">{{ $specification['label'] }}</dt>
                            <dd class="px-4 py-3 text-sm text-slate-950">{{ $specification['value'] }}</dd>
                        </div>
                    @empty
                        <div class="px-4 py-8 text-center text-sm text-slate-500">{{ __('仕様は登録されていません。') }}</div>
                    @endforelse
                </dl>
            </section>
        </div>

        @if ($relatedProducts->isNotEmpty())
            <section class="mt-20 border-t border-slate-300 pt-14">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-[0.18em] text-red-700">{{ __('Related products') }}</p>
                        <h2 class="mt-3 text-2xl font-bold tracking-[-0.025em] text-slate-950">{{ __('同じカテゴリの製品') }}</h2>
                    </div>
                    <a href="{{ \App\Support\PublicSite::route('products.index', ['category' => $product->category->slug]) }}" class="inline-flex min-h-11 items-center text-sm font-bold text-slate-950 hover:text-red-800">
                        {{ __('カテゴリ一覧へ') }} <span class="ml-2 text-red-700" aria-hidden="true">→</span>
                    </a>
                </div>
                <div class="mt-8 grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                    @foreach ($relatedProducts as $relatedProduct)
                        <x-public.product-card :product="$relatedProduct" compact />
                    @endforeach
                </div>
            </section>
        @endif
    </div>
@endsection
