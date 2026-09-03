@extends('layouts.public')

@section('title', __('制作概要'))
@section('meta_description', __('NihonReach の課題設定、設計判断、実装範囲、品質確認とデータポリシーを説明します。'))

@section('content')
    <section class="relative overflow-hidden bg-slate-950 text-white">
        <div class="industrial-grid absolute inset-0 opacity-70"></div>
        <div class="relative mx-auto grid max-w-[1180px] gap-12 px-5 py-20 lg:grid-cols-[.9fr_1.1fr] lg:px-0 lg:py-24">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.2em] text-red-400">{{ __('Portfolio case study') }}</p>
                <h1 class="mt-5 text-4xl font-bold tracking-[-0.045em] sm:text-5xl">{{ __('制作概要') }}</h1>
                <p class="mt-6 max-w-xl text-base leading-8 text-slate-300">
                    {{ __('精密切削工具メーカーの製品サイトと管理業務を題材に、公開カタログから問い合わせ対応までを一つの Laravel アプリケーションとして実装しました。') }}
                </p>
            </div>
            <div class="grid gap-px border border-white/15 bg-white/15 sm:grid-cols-2">
                <div class="bg-slate-950/85 p-5">
                    <p class="text-[10px] font-bold uppercase tracking-[0.15em] text-red-400">{{ __('Public site') }}</p>
                    <p class="mt-3 font-bold">Blade / Tailwind CSS</p>
                </div>
                <div class="bg-slate-950/85 p-5">
                    <p class="text-[10px] font-bold uppercase tracking-[0.15em] text-red-400">{{ __('Admin') }}</p>
                    <p class="mt-3 font-bold">Inertia / Vue / TypeScript</p>
                </div>
                <div class="bg-slate-950/85 p-5">
                    <p class="text-[10px] font-bold uppercase tracking-[0.15em] text-red-400">{{ __('Backend') }}</p>
                    <p class="mt-3 font-bold">Laravel / Eloquent / MySQL</p>
                </div>
                <div class="bg-slate-950/85 p-5">
                    <p class="text-[10px] font-bold uppercase tracking-[0.15em] text-red-400">{{ __('Quality') }}</p>
                    <p class="mt-3 font-bold">PHPUnit / PHPStan / Pint</p>
                </div>
            </div>
        </div>
    </section>

    <div class="mx-auto max-w-[1180px] px-5 py-16 lg:px-0 lg:py-20">
        <section class="grid gap-10 border-b border-slate-300 pb-16 lg:grid-cols-[.65fr_1.35fr]">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.18em] text-red-700">{{ __('01 / Context') }}</p>
                <h2 class="mt-3 text-2xl font-bold tracking-[-0.03em] text-slate-950">{{ __('課題設定') }}</h2>
            </div>
            <div class="grid gap-6 md:grid-cols-2">
                <article class="border-l-2 border-red-700 pl-5">
                    <h3 class="font-bold text-slate-950">{{ __('訪問者側') }}</h3>
                    <p class="mt-3 text-sm leading-7 text-slate-600">
                        {{ __('製品カテゴリ、詳細仕様、対象製品を引き継いだ問い合わせフォームが必要でした。') }}
                    </p>
                </article>
                <article class="border-l-2 border-slate-400 pl-5">
                    <h3 class="font-bold text-slate-950">{{ __('管理者側') }}</h3>
                    <p class="mt-3 text-sm leading-7 text-slate-600">
                        {{ __('分類・製品の CRUD と、問い合わせを new・in progress・closed で管理する画面が必要でした。') }}
                    </p>
                </article>
            </div>
        </section>

        <section class="grid gap-10 border-b border-slate-300 py-16 lg:grid-cols-[.65fr_1.35fr]">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.18em] text-red-700">{{ __('02 / Architecture') }}</p>
                <h2 class="mt-3 text-2xl font-bold tracking-[-0.03em] text-slate-950">{{ __('設計判断') }}</h2>
                <p class="mt-4 text-sm leading-7 text-slate-600">
                    {{ __('現在の規模で説明できる、単純な Laravel 単体構成を選びました。') }}
                </p>
            </div>
            <div>
                <div class="grid gap-px border border-slate-300 bg-slate-300 md:grid-cols-5">
                    <div class="bg-white p-4">
                        <p class="text-[10px] font-bold text-red-700">{{ __('REQUEST') }}</p>
                        <p class="mt-2 text-sm font-bold">Route</p>
                    </div>
                    <div class="bg-white p-4">
                        <p class="text-[10px] font-bold text-red-700">{{ __('BOUNDARY') }}</p>
                        <p class="mt-2 text-sm font-bold">Middleware<br>Form Request</p>
                    </div>
                    <div class="bg-white p-4">
                        <p class="text-[10px] font-bold text-red-700">{{ __('ACTION') }}</p>
                        <p class="mt-2 text-sm font-bold">Controller</p>
                    </div>
                    <div class="bg-white p-4">
                        <p class="text-[10px] font-bold text-red-700">{{ __('DOMAIN') }}</p>
                        <p class="mt-2 text-sm font-bold">Eloquent<br>MySQL</p>
                    </div>
                    <div class="bg-white p-4">
                        <p class="text-[10px] font-bold text-red-700">{{ __('RESPONSE') }}</p>
                        <p class="mt-2 text-sm font-bold">Blade<br>Inertia / Vue</p>
                    </div>
                </div>

                <div class="mt-7 grid gap-5 md:grid-cols-3">
                    <article>
                        <h3 class="font-bold text-slate-950">{{ __('公開サイトは Blade') }}</h3>
                        <p class="mt-2 text-sm leading-7 text-slate-600">{{ __('閲覧中心の製品情報をサーバーで描画し、構造を明確に保ちます。') }}</p>
                    </article>
                    <article>
                        <h3 class="font-bold text-slate-950">{{ __('管理画面は Inertia / Vue') }}</h3>
                        <p class="mt-2 text-sm leading-7 text-slate-600">{{ __('CRUD と状態変更をコンポーネント化しつつ、Laravel の Session 認証を共有します。') }}</p>
                    </article>
                    <article>
                        <h3 class="font-bold text-slate-950">{{ __('独立 API は作らない') }}</h3>
                        <p class="mt-2 text-sm leading-7 text-slate-600">{{ __('現在の要件に不要な認証・通信レイヤーを増やさず、説明可能性を優先しました。') }}</p>
                    </article>
                </div>
            </div>
        </section>

        <section class="grid gap-10 border-b border-slate-300 py-16 lg:grid-cols-[.65fr_1.35fr]">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.18em] text-red-700">{{ __('03 / Implementation') }}</p>
                <h2 class="mt-3 text-2xl font-bold tracking-[-0.03em] text-slate-950">{{ __('実装した範囲') }}</h2>
            </div>
            <div class="grid gap-px border border-slate-200 bg-slate-200 sm:grid-cols-2">
                <article class="bg-white p-6">
                    <p class="text-xs font-bold text-red-700">{{ __('CATALOG') }}</p>
                    <h3 class="mt-4 font-bold text-slate-950">{{ __('分類・製品・公開条件') }}</h3>
                    <p class="mt-3 text-sm leading-7 text-slate-600">{{ __('分類フィルター、製品詳細、JSON 仕様、関連製品、公開状態を実装。') }}</p>
                </article>
                <article class="bg-white p-6">
                    <p class="text-xs font-bold text-red-700">{{ __('INQUIRY') }}</p>
                    <h3 class="mt-4 font-bold text-slate-950">{{ __('入力検証と状態管理') }}</h3>
                    <p class="mt-3 text-sm leading-7 text-slate-600">{{ __('対象製品の再検証、送信制限、問い合わせ保存、完了時刻を管理。') }}</p>
                </article>
                <article class="bg-white p-6">
                    <p class="text-xs font-bold text-red-700">{{ __('ADMIN') }}</p>
                    <h3 class="mt-4 font-bold text-slate-950">{{ __('認証・権限・CRUD') }}</h3>
                    <p class="mt-3 text-sm leading-7 text-slate-600">{{ __('公開登録を止め、管理者ミドルウェアを通した分類・製品・問い合わせ画面を構築。') }}</p>
                </article>
                <article class="bg-white p-6">
                    <p class="text-xs font-bold text-red-700">{{ __('EVIDENCE') }}</p>
                    <h3 class="mt-4 font-bold text-slate-950">{{ __('テストと静的解析') }}</h3>
                    <p class="mt-3 text-sm leading-7 text-slate-600">{{ __('公開可視性、権限、検証、CRUD、状態時間の整合性を自動テスト。') }}</p>
                </article>
            </div>
        </section>

        <section class="grid gap-10 border-b border-slate-300 py-16 lg:grid-cols-[.65fr_1.35fr]">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.18em] text-red-700">{{ __('04 / Data policy') }}</p>
                <h2 class="mt-3 text-2xl font-bold tracking-[-0.03em] text-slate-950">{{ __('データ方針') }}</h2>
            </div>
            <div>
                <p class="border-l-2 border-red-700 pl-5 text-base font-bold leading-8 text-slate-950">
                    {{ __('商用運営中の会社や顧客案件ではありません。') }}
                </p>
                <ul class="mt-6 grid gap-3 text-sm leading-7 text-slate-700">
                    <li class="flex gap-3"><span class="font-bold text-red-700" aria-hidden="true">—</span> {{ __('製品名・型番・説明・仕様は、この Portfolio Demo 用に作成した架空データです。') }}</li>
                    <li class="flex gap-3"><span class="font-bold text-red-700" aria-hidden="true">—</span> {{ __('実在企業の Logo、顧客情報、図面、内部資料、未公開パラメータは使用していません。') }}</li>
                    <li class="flex gap-3"><span class="font-bold text-red-700" aria-hidden="true">—</span> {{ __('工具画像は本プロジェクト用に生成・選定したオリジナル素材で、特定企業の製品を再現していません。') }}</li>
                    <li class="flex gap-3"><span class="font-bold text-red-700" aria-hidden="true">—</span> {{ __('表示件数は Demo データの現在値であり、営業実績や導入実績ではありません。') }}</li>
                </ul>
            </div>
        </section>

        <section class="grid gap-10 pt-16 lg:grid-cols-[.65fr_1.35fr]">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.18em] text-red-700">{{ __('05 / Scope') }}</p>
                <h2 class="mt-3 text-2xl font-bold tracking-[-0.03em] text-slate-950">{{ __('意図した境界') }}</h2>
            </div>
            <div>
                <p class="text-sm leading-8 text-slate-700">
                    {{ __('求職用に一つの通常規模のプロジェクトを完成させることを優先し、Redis、マイクロサービス、独立 API、決済、オブジェクトストレージ、実運用メールは追加していません。問い合わせは Demo データベースへ保存され、実在企業への送信は行われません。') }}
                </p>
                <div class="mt-7 flex flex-col gap-3 sm:flex-row">
                    <a href="{{ \App\Support\PublicSite::route('products.index') }}" class="inline-flex min-h-12 items-center justify-center bg-slate-950 px-6 text-sm font-bold text-white transition hover:bg-red-800">
                        {{ __('製品カタログを見る') }}
                    </a>
                    <a href="{{ \App\Support\PublicSite::route('inquiries.create') }}" class="inline-flex min-h-12 items-center justify-center border border-slate-400 bg-white px-6 text-sm font-bold text-slate-800 transition hover:border-slate-950">
                        {{ __('問い合わせフローを見る') }}
                    </a>
                </div>
            </div>
        </section>
    </div>
@endsection
