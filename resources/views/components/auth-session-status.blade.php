@props(['status'])

@if ($status)
    <div {{ $attributes->merge(['class' => 'auth-session-status']) }}>
        {{ $status }}
    </div>
@endif

@once
<style>
    .auth-session-status { font-family: var(--font-mono); font-size: 11px; letter-spacing: 0.04em; color: var(--risk-min); padding: 10px 14px; border: 1px solid var(--risk-min); background: var(--risk-min-bg); border-radius: var(--r-sm); }
</style>
@endonce
