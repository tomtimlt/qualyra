<x-app-layout>
    <x-slot name="header">
        Cervus / <b>Profil</b>
    </x-slot>

    <div class="profile-page">
        <div class="profile-page__head">
            <div class="eyebrow eyebrow--accent">Compte · Réglages</div>
            <h1>Profil.</h1>
            <p class="lead">Mettez à jour votre identité, votre mot de passe ou supprimez votre compte. La suppression entraîne aussi la perte de votre organisation et de tous ses usages déclarés.</p>
        </div>

        <div class="surface">
            <div class="surface__head"><h3>Informations</h3></div>
            <div class="surface__body">
                @include('profile.partials.update-profile-information-form')
            </div>
        </div>

        <div class="surface">
            <div class="surface__head"><h3>Mot de passe</h3></div>
            <div class="surface__body">
                @include('profile.partials.update-password-form')
            </div>
        </div>

        <div class="surface surface--danger">
            <div class="surface__head"><h3>Zone sensible</h3></div>
            <div class="surface__body">
                @include('profile.partials.delete-user-form')
            </div>
        </div>
    </div>

    <style>
        h1 { font-family: var(--font-display); font-size: 56px; line-height: 1; letter-spacing: -0.02em; margin: 8px 0 0; color: var(--text); }

        .profile-page { max-width: 760px; display: flex; flex-direction: column; gap: 24px; }
        .profile-page__head { padding-bottom: 24px; border-bottom: 1px solid var(--hairline); margin-bottom: 8px; }
        .profile-page__head .lead { margin-top: 16px; max-width: 60ch; }

        .surface { border: 1px solid var(--hairline); border-radius: var(--r-md); background: var(--ink-950); overflow: hidden; }
        .surface__head { padding: 18px 24px; border-bottom: 1px solid var(--hairline); }
        .surface__head h3 { margin: 0; font-size: 15px; font-weight: 500; letter-spacing: -0.01em; color: var(--text); }
        .surface__body { padding: 28px; }
        .surface--danger { border-left: 3px solid var(--risk-inacc); }

        /* Generic form helpers used by partials */
        .form-section { display: flex; flex-direction: column; gap: 8px; margin-bottom: 24px; }
        .form-section__title { font-family: var(--font-display); font-size: 22px; line-height: 1.15; letter-spacing: -0.01em; color: var(--text); margin: 0; }
        .form-section__sub { color: var(--text-muted); font-size: 13px; line-height: 1.55; margin: 0; }

        .form-stack-block { display: flex; flex-direction: column; gap: 18px; max-width: 520px; }
        .form-actions-row { display: flex; align-items: center; gap: 16px; padding-top: 16px; }
        .saved-flash { font-family: var(--font-mono); font-size: 11px; color: var(--risk-min); letter-spacing: 0.06em; text-transform: uppercase; }

        .help-link { background: none; border: none; padding: 0; color: var(--accent-soft); font-size: 12px; cursor: pointer; text-decoration: underline; text-underline-offset: 3px; font-family: inherit; }
        .help-link:hover { color: var(--accent); }

        @media (max-width: 720px) {
            h1 { font-size: 40px; }
            .surface__body { padding: 20px; }
        }
    </style>
</x-app-layout>
