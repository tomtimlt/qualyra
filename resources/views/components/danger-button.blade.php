<button {{ $attributes->merge(['type' => 'submit', 'class' => 'btn btn--danger']) }}>
    {{ $slot }}
</button>

@once
<style>
    .btn--danger { background: var(--risk-inacc); color: #fff; border-color: var(--risk-inacc); }
    .btn--danger:hover { background: #b8333d; border-color: #b8333d; }
</style>
@endonce
