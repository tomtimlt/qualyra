<x-app-layout>
    <x-slot name="header">
        <a href="{{ route('usages.index') }}" style="color: inherit; text-decoration: none">Qualyra / Mes usages IA</a> / <b>Modifier</b>
    </x-slot>

    <div class="form-page">
        <div class="form-page__head">
            <div class="eyebrow eyebrow--accent">Édition · {{ $aiUsage->type }}</div>
            <h1>{{ $aiUsage->name }}</h1>
            <p class="lead">
                Toute modification ne s'applique qu'aux nouveaux rapports générés. Les rapports déjà générés
                conservent l'état de l'usage au moment de leur création (snapshot).
            </p>
        </div>

        <form method="POST" action="{{ route('usages.update', $aiUsage) }}" class="form-card">
            @csrf
            @method('PATCH')
            @include('usages._form', ['aiUsage' => $aiUsage])

            <div class="form-actions">
                <a href="{{ route('usages.show', $aiUsage) }}" class="btn-link">← Annuler</a>
                <x-primary-button>Mettre à jour</x-primary-button>
            </div>
        </form>
    </div>

    <style>
        .form-page { max-width: 720px; }
        .form-page__head { margin-bottom: 32px; }
        .form-page__head h1 { font-family: var(--font-display); font-size: 48px; line-height: 1.05; letter-spacing: -0.02em; margin: 8px 0 0; color: var(--text); }
        .form-page__head .lead { margin-top: 16px; max-width: 60ch; }

        .form-card { border: 1px solid var(--hairline); border-radius: var(--r-md); background: var(--ink-950); padding: 32px; display: flex; flex-direction: column; gap: 24px; }
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .help { font-size: 11px; color: var(--text-dim); margin-top: 6px; line-height: 1.5; }

        .form-actions { display: flex; align-items: center; justify-content: flex-end; gap: 16px; padding-top: 24px; border-top: 1px solid var(--hairline); }
        .btn-link { color: var(--text-muted); font-size: 13px; text-decoration: none; transition: color var(--d-fast); }
        .btn-link:hover { color: var(--text); }

        @media (max-width: 720px) {
            .form-grid { grid-template-columns: 1fr; }
            .form-page__head h1 { font-size: 36px; }
        }
    </style>
</x-app-layout>
