@php $route = auth()->check() ? route('profile.theme') : null; @endphp
<button
    type="button"
    class="theme-toggle"
    x-data="{
        current: document.documentElement.dataset.theme || 'dark',
        toggle() {
            const next = this.current === 'light' ? 'dark' : 'light';
            document.documentElement.dataset.theme = next;
            localStorage.setItem('qualyra-theme', next);
            this.current = next;
            @if ($route)
            fetch('{{ $route }}', {
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ theme: next }),
            });
            @endif
        }
    }"
    @click="toggle()"
    :aria-label="current === 'light' ? 'Passer en thème sombre' : 'Passer en thème clair'"
    :title="current === 'light' ? 'Thème sombre' : 'Thème clair'">
    <svg x-show="current === 'dark'" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="16" height="16">
        <circle cx="12" cy="12" r="4"/>
        <path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M4.93 19.07l1.41-1.41M17.66 6.34l1.41-1.41"/>
    </svg>
    <svg x-show="current === 'light'" style="display:none" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="16" height="16">
        <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>
    </svg>
</button>
