@extends('layouts.public')

@section('title', __('お問い合わせ'))
@section('meta_description', __('NihonReach Portfolio Demo の製品問い合わせフォームです。送信内容は Demo データベースへ保存されます。'))

@section('content')
    @php
        $fieldClass = 'mt-2 min-h-12 w-full border border-slate-300 bg-white px-3 py-3 text-sm text-slate-950 outline-none transition placeholder:text-slate-400 focus:border-red-700 focus:ring-2 focus:ring-red-100';
        // Validation errors may flash arrays; text fields must only echo scalar values.
        $oldText = static function (string $key, mixed $default = null): string {
            $value = old($key, $default);

            return is_scalar($value) ? (string) $value : '';
        };
    @endphp

    <section class="metal-sheen border-b border-slate-300">
        <div class="mx-auto max-w-[1180px] px-5 py-14 lg:px-0 lg:py-16">
            <nav class="flex items-center gap-2 text-xs font-medium text-slate-500" aria-label="{{ __('パンくず') }}">
                <a href="{{ \App\Support\PublicSite::route('home') }}" class="hover:text-red-800">{{ __('ホーム') }}</a>
                <span aria-hidden="true">/</span>
                <span aria-current="page" class="text-slate-800">{{ __('お問い合わせ') }}</span>
            </nav>
            <div class="mt-8 grid gap-6 lg:grid-cols-[.7fr_1.3fr] lg:items-end">
                <div>
                    <p class="text-xs font-bold uppercase tracking-[0.18em] text-red-700">{{ __('Inquiry form') }}</p>
                    <h1 class="mt-3 text-4xl font-bold tracking-[-0.04em] text-slate-950 sm:text-5xl">{{ __('お問い合わせ') }}</h1>
                </div>
                <p class="max-w-2xl text-sm leading-7 text-slate-600 lg:justify-self-end">
                    {{ __('製品を選択して Demo 問い合わせを登録し、管理画面で対応状況を確認できます。') }}
                </p>
            </div>
        </div>
    </section>

    <div class="mx-auto grid max-w-[1180px] gap-8 px-5 py-12 lg:grid-cols-[1fr_21rem] lg:items-start lg:px-0 lg:py-16">
        <form method="POST" action="{{ \App\Support\PublicSite::route('inquiries.store') }}" class="border border-slate-300 bg-white p-5 sm:p-8">
            @csrf

            <div class="flex flex-col gap-3 border-b border-slate-200 pb-6 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p class="text-xs font-bold uppercase tracking-[0.14em] text-red-700">{{ __('Required fields') }}</p>
                    <h2 class="mt-2 text-xl font-bold text-slate-950">{{ __('相談内容を入力') }}</h2>
                </div>
                <p class="text-xs text-slate-500"><span class="font-bold text-red-700">*</span> {{ __('は必須項目です') }}</p>
            </div>

            @if ($errors->any())
                <div class="mt-6 border-l-2 border-red-700 bg-red-50 p-4 text-sm font-bold text-red-900" role="alert" tabindex="-1">
                    {{ __('入力内容を確認してください。エラーのある項目に説明を表示しています。') }}
                </div>
            @endif

            <div class="mt-7 grid gap-6 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <label for="product_id" class="block text-sm font-bold text-slate-800">{{ __('対象製品') }}</label>
                    <select
                        id="product_id"
                        name="product_id"
                        class="{{ $fieldClass }}"
                        @error('product_id') aria-invalid="true" aria-describedby="product_id-error" @enderror
                    >
                        <option value="">{{ __('製品を指定しない') }}</option>
                        @foreach ($products as $product)
                            <option value="{{ $product->id }}" @selected($oldText('product_id', $selectedProduct?->id) === (string) $product->id)>
                                {{ $product->category->translated('name') }} / {{ $product->translated('name') }}（{{ $product->sku }}）
                            </option>
                        @endforeach
                    </select>
                    @error('product_id') <p id="product_id-error" class="mt-2 text-sm font-medium text-red-700">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="name" class="block text-sm font-bold text-slate-800">{{ __('お名前') }} <span class="text-red-700">*</span></label>
                    <input
                        id="name"
                        name="name"
                        value="{{ $oldText('name') }}"
                        required
                        maxlength="100"
                        autocomplete="name"
                        class="{{ $fieldClass }}"
                        @error('name') aria-invalid="true" aria-describedby="name-error" @enderror
                    >
                    @error('name') <p id="name-error" class="mt-2 text-sm font-medium text-red-700">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="company" class="block text-sm font-bold text-slate-800">{{ __('会社名') }}</label>
                    <input
                        id="company"
                        name="company"
                        value="{{ $oldText('company') }}"
                        maxlength="150"
                        autocomplete="organization"
                        class="{{ $fieldClass }}"
                        @error('company') aria-invalid="true" aria-describedby="company-error" @enderror
                    >
                    @error('company') <p id="company-error" class="mt-2 text-sm font-medium text-red-700">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="email" class="block text-sm font-bold text-slate-800">{{ __('メールアドレス') }} <span class="text-red-700">*</span></label>
                    <input
                        id="email"
                        type="email"
                        name="email"
                        value="{{ $oldText('email') }}"
                        required
                        maxlength="255"
                        autocomplete="email"
                        class="{{ $fieldClass }}"
                        @error('email') aria-invalid="true" aria-describedby="email-error" @enderror
                    >
                    @error('email') <p id="email-error" class="mt-2 text-sm font-medium text-red-700">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="phone" class="block text-sm font-bold text-slate-800">{{ __('電話番号') }}</label>
                    <input
                        id="phone"
                        name="phone"
                        value="{{ $oldText('phone') }}"
                        maxlength="50"
                        autocomplete="tel"
                        class="{{ $fieldClass }}"
                        @error('phone') aria-invalid="true" aria-describedby="phone-error" @enderror
                    >
                    @error('phone') <p id="phone-error" class="mt-2 text-sm font-medium text-red-700">{{ $message }}</p> @enderror
                </div>

                <div class="sm:col-span-2">
                    <label for="subject" class="block text-sm font-bold text-slate-800">{{ __('件名') }} <span class="text-red-700">*</span></label>
                    <input
                        id="subject"
                        name="subject"
                        value="{{ $oldText('subject') }}"
                        required
                        maxlength="150"
                        class="{{ $fieldClass }}"
                        @error('subject') aria-invalid="true" aria-describedby="subject-error" @enderror
                    >
                    @error('subject') <p id="subject-error" class="mt-2 text-sm font-medium text-red-700">{{ $message }}</p> @enderror
                </div>

                <div class="sm:col-span-2">
                    <label for="message" class="block text-sm font-bold text-slate-800">{{ __('お問い合わせ内容') }} <span class="text-red-700">*</span></label>
                    <textarea
                        id="message"
                        name="message"
                        rows="7"
                        required
                        minlength="20"
                        maxlength="5000"
                        class="{{ $fieldClass }} leading-7"
                        @error('message') aria-invalid="true" aria-describedby="message-error" @enderror
                    >{{ $oldText('message') }}</textarea>
                    <div class="mt-2 flex justify-between gap-4 text-xs text-slate-500">
                        @error('message')
                            <p id="message-error" class="font-medium text-red-700">{{ $message }}</p>
                        @else
                            <p>{{ __('相談内容や確認したい仕様を 20 文字以上で入力してください。') }}</p>
                        @enderror
                        <span class="shrink-0">{{ __('最大 5,000 文字') }}</span>
                    </div>
                </div>
            </div>

            <div class="mt-6 border border-slate-200 bg-slate-50 p-4">
                <label for="privacy" class="flex cursor-pointer items-start gap-3 text-sm leading-6 text-slate-700">
                    <input
                        id="privacy"
                        type="checkbox"
                        name="privacy"
                        value="1"
                        required
                        @checked($oldText('privacy') === '1')
                        class="mt-1 size-4 shrink-0 rounded-sm border-slate-400 text-red-700 focus:ring-red-700"
                        @error('privacy') aria-invalid="true" aria-describedby="privacy-error" @enderror
                    >
                    <span>{{ __('入力内容がこの Portfolio Demo のローカルデータベースに保存されることに同意します。') }}<span class="text-red-700">*</span></span>
                </label>
                @error('privacy') <p id="privacy-error" class="mt-2 pl-7 text-sm font-medium text-red-700">{{ $message }}</p> @enderror
            </div>

            <button type="submit" class="mt-6 inline-flex min-h-12 w-full items-center justify-center bg-red-700 px-6 text-sm font-bold text-white transition hover:bg-red-800 sm:w-auto">
                {{ __('Demo お問い合わせを送信') }}
                <span class="ml-3" aria-hidden="true">→</span>
            </button>
        </form>

        <aside class="space-y-5 lg:sticky lg:top-32">
            @if ($selectedProduct)
                <div class="border border-slate-300 bg-white">
                    <x-public.product-media :product="$selectedProduct" />
                    <div class="p-5">
                        <p class="text-[10px] font-bold uppercase tracking-[0.14em] text-red-700">{{ __('Selected product') }}</p>
                        <p class="mt-2 text-sm font-bold leading-6 text-slate-950">{{ $selectedProduct->translated('name') }}</p>
                        <p class="mt-2 font-mono text-xs text-slate-500">{{ $selectedProduct->sku }}</p>
                    </div>
                </div>
            @endif

            <div class="border border-amber-300 bg-amber-50 p-5 text-sm leading-7 text-amber-950">
                <p class="font-bold">{{ __('Demo データについて') }}</p>
                <p class="mt-2">{{ __('送信内容は開発用データベースに保存されますが、実在企業への送信や営業対応は行われません。') }}</p>
                <p class="mt-4 font-medium">{{ __('実在する個人・会社の情報は入力しないでください。') }}</p>
            </div>

            <ol class="border border-slate-300 bg-slate-950 p-5 text-white">
                <li class="border-b border-white/15 pb-4">
                    <span class="font-mono text-[10px] text-red-400">01</span>
                    <p class="mt-1 text-sm font-bold">{{ __('フォームを検証') }}</p>
                </li>
                <li class="border-b border-white/15 py-4">
                    <span class="font-mono text-[10px] text-red-400">02</span>
                    <p class="mt-1 text-sm font-bold">{{ __('MySQL に保存') }}</p>
                </li>
                <li class="pt-4">
                    <span class="font-mono text-[10px] text-red-400">03</span>
                    <p class="mt-1 text-sm font-bold">{{ __('管理画面で状態更新') }}</p>
                </li>
            </ol>
        </aside>
    </div>
@endsection
