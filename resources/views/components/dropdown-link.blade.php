<a {{ $attributes->merge(['class' => 'dropdown-link']) }}>{{ $slot }}</a>

@once
<style>
    .dropdown-link { display: block; width: 100%; padding: 10px 16px; font-size: 13px; color: var(--ink-300); text-decoration: none; text-align: left; transition: all var(--d-fast); border: none; background: transparent; cursor: pointer; font-family: inherit; }
    .dropdown-link:hover { background: var(--ink-900); color: var(--text); }
</style>
@endonce
