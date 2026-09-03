@props([
    'inverse' => false,
    'tagline' => true,
])

<span {{ $attributes->class(['inline-flex items-center gap-3']) }}>
    <svg class="size-10 shrink-0" viewBox="0 0 44 44" aria-hidden="true">
        <path d="M4 4h23l13 13v23H17L4 27V4Z" fill="#b91c1c"/>
        <path d="M13 30V14h4.5l9.5 9.8V14h4v16h-4.2L17 19.8V30h-4Z" fill="white"/>
    </svg>
    <span class="min-w-0">
        <span @class([
            'block text-lg font-bold leading-none tracking-[-0.035em]',
            'text-white' => $inverse,
            'text-slate-950' => ! $inverse,
        ])>
            NihonReach
        </span>
        @if ($tagline)
            <span @class([
                'mt-1.5 block truncate text-[9px] font-semibold uppercase tracking-[0.2em]',
                'text-slate-400' => $inverse,
                'text-slate-500' => ! $inverse,
            ])>
                {{ __('Precision tools · Portfolio') }}
            </span>
        @endif
    </span>
</span>
