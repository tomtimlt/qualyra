<x-app-layout>
    <x-slot name="header">
        <a href="{{ route('vendors.index') }}" style="color: inherit; text-decoration: none">Qualyra / Fournisseurs IA</a> / <b>Nouveau</b>
    </x-slot>

    <div class="form-page">
        <div class="form-page__head">
            <div class="eyebrow eyebrow--accent">Fournisseurs IA · Déclaration</div>
            <h1>Déclarer un <em>fournisseur.</em></h1>
            <p class="lead">
                Renseignez les informations contractuelles et de conformité du fournisseur.
                Vous pourrez ensuite rattacher vos usages IA à ce fournisseur.
            </p>
        </div>

        <form method="POST" action="{{ route('vendors.store') }}" class="form-card">
            @csrf
            @include('vendors._form')

            <div class="form-actions">
                <a href="{{ route('vendors.index') }}" class="btn-link">← Annuler</a>
                <x-primary-button>Enregistrer</x-primary-button>
            </div>
        </form>
    </div>

    <style>
        .form-page { max-width: 720px; }
        .form-page__head { margin-bottom: 32px; }
        .form-page__head h1 { font-family: var(--font-display); font-size: 56px; line-height: 1.05; letter-spacing: -0.02em; margin: 8px 0 0; color: var(--text); }
        .form-page__head h1 em { color: var(--accent); font-style: italic; }
        .form-page__head .lead { margin-top: 16px; max-width: 60ch; }

        .form-card { border: 1px solid var(--hairline); border-radius: var(--r-md); background: var(--surface); padding: 32px; display: flex; flex-direction: column; gap: 24px; }
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .form-checks { display: flex; flex-direction: column; gap: 10px; padding: 16px; border: 1px solid var(--hairline); border-radius: var(--r-sm); background: var(--surface-2); }
        .check { display: flex; align-items: flex-start; gap: 10px; font-size: 13px; color: var(--text); cursor: pointer; line-height: 1.4; }
        .check input { margin-top: 2px; flex-shrink: 0; }
        .help { font-size: 11px; color: var(--text-dim); margin-top: 6px; line-height: 1.5; }

        .form-actions { display: flex; align-items: center; justify-content: flex-end; gap: 16px; padding-top: 24px; border-top: 1px solid var(--hairline); }
        .btn-link { color: var(--text-muted); font-size: 13px; text-decoration: none; transition: color var(--d-fast); }
        .btn-link:hover { color: var(--text); }

        @media (max-width: 720px) {
            .form-grid { grid-template-columns: 1fr; }
            .form-page__head h1 { font-size: 40px; }
        }
    </style>
</x-app-layout>
