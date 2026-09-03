@props([
    'product',
    'compact' => false,
])

@php
    $specifications = $product->translated('specifications');
@endphp

<a
    href="{{ \App\Support\PublicSite::route('products.show', $product->slug) }}"
    class="group block h-full border border-slate-200 bg-white transition duration-300 hover:-translate-y-0.5 hover:border-slate-400 hover:shadow-[0_16px_45px_-28px_rgba(15,23,42,.55)] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-red-700 focus-visible:ring-offset-4"
>
    <article class="flex h-full flex-col">
        <x-public.product-media :product="$product" />

        <div @class(['flex flex-1 flex-col', 'p-5 sm:p-6' => ! $compact, 'p-5' => $compact])>
            <div class="flex flex-wrap items-center justify-between gap-2 text-[11px]">
                <span class="font-bold uppercase tracking-[0.12em] text-red-700">{{ $product->category->translated('name') }}</span>
                <span class="font-mono text-slate-500">{{ $product->sku }}</span>
            </div>

            <h3 @class([
                'mt-3 font-bold leading-7 tracking-tight text-slate-950 transition-colors group-hover:text-red-800',
                'text-lg' => ! $compact,
                'text-base' => $compact,
            ])>
                {{ $product->translated('name') }}
            </h3>
            <p class="mt-2 flex-1 text-sm leading-6 text-slate-600">{{ $product->translated('summary') }}</p>

            @if (! $compact && filled($specifications))
                <dl class="mt-5 grid grid-cols-2 gap-px border border-slate-200 bg-slate-200">
                    @foreach (collect($specifications)->take(2) as $specification)
                        <div class="bg-slate-50 px-3 py-2.5">
                            <dt class="text-[10px] font-semibold tracking-wide text-slate-500">{{ $specification['label'] }}</dt>
                            <dd class="mt-1 text-xs font-bold text-slate-900">{{ $specification['value'] }}</dd>
                        </div>
                    @endforeach
                </dl>
            @endif

            <span class="mt-5 inline-flex items-center gap-2 text-sm font-bold text-slate-950 transition-colors group-hover:text-red-800">
                {{ __('製品詳細') }} <span class="text-red-700" aria-hidden="true">→</span>
            </span>
        </div>
    </article>
</a>
