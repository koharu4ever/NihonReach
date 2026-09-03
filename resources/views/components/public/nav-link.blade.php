@props([
    'href',
    'active' => false,
])

<a
    href="{{ $href }}"
    @if ($active) aria-current="page" @endif
    {{ $attributes->class([
        'relative inline-flex min-h-11 items-center border-b-2 px-1 text-sm font-semibold transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-red-700 focus-visible:ring-offset-4',
        'border-red-700 text-slate-950' => $active,
        'border-transparent text-slate-600 hover:border-slate-300 hover:text-slate-950' => ! $active,
    ]) }}
>
    {{ $slot }}
</a>
