@props(['messages'])

@if ($messages)
    <ul {{ $attributes->merge(['class' => 'input-error']) }}>
        @foreach ((array) $messages as $message)
            <li>{{ $message }}</li>
        @endforeach
    </ul>
@endif

@once
<style>
    .input-error { font-family: var(--font-mono); font-size: 11px; letter-spacing: 0.04em; color: var(--risk-inacc); list-style: none; padding: 0; margin: 6px 0 0; display: flex; flex-direction: column; gap: 4px; }
    .input-error li::before { content: '✕  '; color: var(--risk-inacc); }
</style>
@endonce
