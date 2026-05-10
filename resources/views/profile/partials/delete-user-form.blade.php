<section>
    <div class="form-section">
        <h4 class="form-section__title">Supprimer le compte</h4>
        <p class="form-section__sub">
            La suppression est irréversible : votre organisation, vos usages déclarés, vos réponses au
            questionnaire et l'historique de vos rapports seront définitivement effacés.
        </p>
    </div>

    <x-danger-button
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
    >Supprimer mon compte</x-danger-button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="modal-form">
            @csrf
            @method('delete')

            <div class="eyebrow eyebrow--accent" style="color: var(--risk-inacc); margin-bottom: 12px">Action irréversible</div>
            <h2 class="modal-form__title">Supprimer le compte ?</h2>

            <p class="modal-form__sub">
                Toutes les données (organisation, usages, réponses, rapports) seront perdues.
                Saisissez votre mot de passe pour confirmer.
            </p>

            <div style="margin-top: 24px">
                <x-input-label for="password" value="Mot de passe" class="sr-only" />
                <x-text-input
                    id="password"
                    name="password"
                    type="password"
                    placeholder="Mot de passe"
                />
                <x-input-error :messages="$errors->userDeletion->get('password')" />
            </div>

            <div class="modal-form__actions">
                <x-secondary-button x-on:click="$dispatch('close')">
                    Annuler
                </x-secondary-button>

                <x-danger-button>
                    Supprimer définitivement
                </x-danger-button>
            </div>
        </form>
    </x-modal>
</section>

<style>
    .modal-form { padding: 32px; }
    .modal-form__title { font-family: var(--font-display); font-size: 28px; line-height: 1.1; letter-spacing: -0.015em; color: var(--text); margin: 0 0 8px; }
    .modal-form__sub { color: var(--text-muted); font-size: 13px; line-height: 1.55; margin: 0; max-width: 60ch; }
    .modal-form__actions { display: flex; justify-content: flex-end; gap: 12px; margin-top: 24px; padding-top: 20px; border-top: 1px solid var(--hairline); }
</style>
