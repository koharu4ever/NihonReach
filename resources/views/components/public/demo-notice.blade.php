<div class="border-b border-red-900/20 bg-slate-950 text-slate-200" role="note">
    <div class="mx-auto flex min-h-9 max-w-[1180px] items-center justify-center gap-3 px-5 py-2 text-center text-[11px] leading-5 sm:justify-between sm:text-left lg:px-0">
        <p>
            <span class="mr-2 inline-flex border border-red-500/50 px-2 py-0.5 font-bold tracking-[0.14em] text-red-300">DEMO</span>
            {{ __('Portfolio Demo — 企業・製品・仕様はすべて架空のデータです。') }}
        </p>
        <a href="{{ \App\Support\PublicSite::route('about') }}" class="hidden shrink-0 font-semibold text-white underline decoration-red-500 underline-offset-4 hover:text-red-200 sm:inline-flex">
            {{ __('制作範囲を確認') }}
        </a>
    </div>
</div>
