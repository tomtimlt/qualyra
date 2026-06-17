<button {{ $attributes->merge(['type' => 'submit', 'class' => 'btn btn--danger']) }}>
    {{ $slot }}
</button>

@once
<style>
    .btn--danger { background: var(--risk-inacc); color: #fff; border-color: var(--risk-inacc); }
    .btn--danger:hover { background: color-mix(in srgb, var(--risk-inacc) 80%, #fff); border-color: color-mix(in srgb, var(--risk-inacc) 80%, #fff); }
</style>
@endonce
