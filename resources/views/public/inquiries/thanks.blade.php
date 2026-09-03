@extends('layouts.public')

@section('title', __('送信完了'))
@section('meta_description', __('Demo 問い合わせが開発用データベースへ保存されました。'))

@section('content')
    <section class="relative overflow-hidden bg-slate-950 text-white">
        <div class="industrial-grid absolute inset-0 opacity-70"></div>
        <div class="relative mx-auto max-w-3xl px-5 py-24 text-center lg:px-0 lg:py-28">
            <span class="mx-auto grid size-16 place-items-center border border-red-500/50 bg-red-700 text-2xl font-bold text-white" aria-hidden="true">✓</span>
            <p class="mt-7 text-xs font-bold uppercase tracking-[0.18em] text-red-400">{{ __('Demo inquiry saved') }}</p>
            <h1 class="mt-3 text-3xl font-bold tracking-[-0.035em] sm:text-4xl">{{ __('送信が完了しました') }}</h1>
            <p class="mx-auto mt-5 max-w-xl text-sm leading-8 text-slate-300">
                {{ __('お問い合わせは Portfolio Demo のデータベースに保存されました。実際の営業返信やメール送信は行われません。') }}
            </p>
            <div class="mt-9 flex flex-col justify-center gap-3 sm:flex-row">
                <a href="{{ \App\Support\PublicSite::route('products.index') }}" class="inline-flex min-h-12 items-center justify-center bg-red-700 px-6 text-sm font-bold text-white transition hover:bg-red-600">
                    {{ __('製品一覧へ戻る') }}
                </a>
                <a href="{{ \App\Support\PublicSite::route('home') }}" class="inline-flex min-h-12 items-center justify-center border border-white/40 px-6 text-sm font-bold text-white transition hover:border-white hover:bg-white/10">
                    {{ __('ホームへ戻る') }}
                </a>
            </div>
        </div>
    </section>

    <section class="mx-auto max-w-[1180px] px-5 py-14 lg:px-0">
        <div class="grid gap-px border border-slate-200 bg-slate-200 sm:grid-cols-3">
            <div class="bg-white p-5">
                <p class="text-[10px] font-bold uppercase tracking-[0.14em] text-red-700">{{ __('Stored') }}</p>
                <p class="mt-2 text-sm font-bold text-slate-950">{{ __('問い合わせを MySQL に保存') }}</p>
            </div>
            <div class="bg-white p-5">
                <p class="text-[10px] font-bold uppercase tracking-[0.14em] text-red-700">{{ __('Status') }}</p>
                <p class="mt-2 text-sm font-bold text-slate-950">{{ __('初期状態は new') }}</p>
            </div>
            <div class="bg-white p-5">
                <p class="text-[10px] font-bold uppercase tracking-[0.14em] text-red-700">{{ __('Next') }}</p>
                <p class="mt-2 text-sm font-bold text-slate-950">{{ __('管理画面から対応状況を更新') }}</p>
            </div>
        </div>
    </section>
@endsection
