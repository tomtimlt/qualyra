<x-app-layout>
    <x-slot name="header">
        <a href="{{ route('organization.show') }}" style="color: inherit; text-decoration: none">Qualyra / Organisation</a> / <b>Modifier</b>
    </x-slot>

    <div class="form-page">
        <div class="form-page__head">
            <div class="eyebrow eyebrow--accent">Compte · Édition</div>
            <h1>Modifier <em>{{ $organization->name }}</em>.</h1>
            <p class="lead">
                Les changements n'altèrent jamais un rapport déjà généré : chaque rapport conserve
                un snapshot des informations au moment de sa création.
            </p>
        </div>

        <form method="POST" action="{{ route('organization.update') }}" class="form-card">
            @csrf
            @method('PATCH')

            <div class="form-grid">
                <div>
                    <x-input-label for="name" value="Nom de l'organisation *" />
                    <x-text-input id="name" name="name" type="text" required autofocus
                                  :value="old('name', $organization->name)" style="margin-top: 6px" />
                    <x-input-error :messages="$errors->get('name')" />
                </div>

                <div>
                    <x-input-label for="siret" value="SIRET (facultatif)" />
                    <x-text-input id="siret" name="siret" type="text" maxlength="14"
                                  :value="old('siret', $organization->siret)" placeholder="14 chiffres"
                                  style="margin-top: 6px; font-family: var(--font-mono)" />
                    <p class="help">14 chiffres. Apparaîtra en couverture du rapport.</p>
                    <x-input-error :messages="$errors->get('siret')" />
                </div>
            </div>

            <div class="form-grid">
                <div>
                    <x-input-label for="size" value="Effectif *" />
                    <select id="size" name="size" required class="input" style="margin-top: 6px">
                        <option value="">— Sélectionner —</option>
                        @foreach (['1-19', '20-49', '50-149', '150+'] as $sizeOption)
                            <option value="{{ $sizeOption }}" @selected(old('size', $organization->size) === $sizeOption)>
                                {{ $sizeOption }} salariés
                            </option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('size')" />
                </div>

                <div>
                    <x-input-label for="sector" value="Secteur d'activité (facultatif)" />
                    <x-text-input id="sector" name="sector" type="text"
                                  :value="old('sector', $organization->sector)" placeholder="Ex : Industrie, Santé, RH"
                                  style="margin-top: 6px" />
                    <x-input-error :messages="$errors->get('sector')" />
                </div>
            </div>

            <div class="form-actions">
                <a href="{{ route('organization.show') }}" class="btn-link">← Annuler</a>
                <x-primary-button>Enregistrer les changements</x-primary-button>
            </div>
        </form>
    </div>

    <style>
        .form-page { max-width: 720px; }
        .form-page__head { margin-bottom: 32px; }
        .form-page__head h1 { font-family: var(--font-display); font-size: 48px; line-height: 1.05; letter-spacing: -0.02em; margin: 8px 0 0; color: var(--text); }
        .form-page__head h1 em { color: var(--accent); font-style: italic; }
        .form-page__head .lead { margin-top: 16px; max-width: 60ch; }

        .form-card { border: 1px solid var(--hairline); border-radius: var(--r-md); background: var(--surface); padding: 32px; display: flex; flex-direction: column; gap: 24px; }
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
