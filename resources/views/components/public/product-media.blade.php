@props([
    'product',
    'eager' => false,
    'variant' => 'card',
])

@php
    $isDetail = $variant === 'detail';
    $imageClasses = $isDetail
        ? 'aspect-[4/3] w-full object-cover object-center'
        : 'aspect-[4/3] w-full object-cover object-center transition duration-500 group-hover:scale-[1.025]';
    $sizes = $isDetail
        ? '(min-width: 1024px) 50vw, 100vw'
        : '(min-width: 1024px) 33vw, (min-width: 768px) 50vw, 100vw';
@endphp

<div {{ $attributes->class(['relative overflow-hidden bg-[#eef0f2]']) }}>
    @if ($product->image_path)
        <img
            src="{{ asset(ltrim($product->image_path, '/')) }}"
            alt="{{ $product->translated('name') }}"
            width="1200"
            height="900"
            loading="{{ $eager ? 'eager' : 'lazy' }}"
            decoding="async"
            sizes="{{ $sizes }}"
            @if ($eager) fetchpriority="high" @endif
            class="{{ $imageClasses }}"
        >
    @else
        <div class="industrial-grid grid aspect-[4/3] place-items-center bg-slate-900 text-white">
            <div class="text-center">
                <p class="text-4xl font-bold tracking-tight">{{ str($product->sku)->afterLast('-') }}</p>
                <p class="mt-3 text-[10px] font-semibold uppercase tracking-[0.26em] text-slate-300">{{ __('Demo precision tool') }}</p>
            </div>
        </div>
    @endif
    <span class="pointer-events-none absolute inset-0 ring-1 ring-inset ring-slate-950/10" aria-hidden="true"></span>
</div>
