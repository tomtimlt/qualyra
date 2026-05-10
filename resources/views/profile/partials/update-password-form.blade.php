<section>
    <div class="form-section">
        <h4 class="form-section__title">Changer le mot de passe</h4>
        <p class="form-section__sub">Choisissez un mot de passe long et que vous n'utilisez pas ailleurs.</p>
    </div>

    <form method="post" action="{{ route('password.update') }}" class="form-stack-block">
        @csrf
        @method('put')

        <div>
            <x-input-label for="update_password_current_password" value="Mot de passe actuel" />
            <x-text-input id="update_password_current_password" name="current_password" type="password" autocomplete="current-password" style="margin-top: 6px" />
            <x-input-error :messages="$errors->updatePassword->get('current_password')" />
        </div>

        <div>
            <x-input-label for="update_password_password" value="Nouveau mot de passe" />
            <x-text-input id="update_password_password" name="password" type="password" autocomplete="new-password" style="margin-top: 6px" />
            <x-input-error :messages="$errors->updatePassword->get('password')" />
        </div>

        <div>
            <x-input-label for="update_password_password_confirmation" value="Confirmer" />
            <x-text-input id="update_password_password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" style="margin-top: 6px" />
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" />
        </div>

        <div class="form-actions-row">
            <x-primary-button>Mettre à jour</x-primary-button>

            @if (session('status') === 'password-updated')
                <p x-data="{ show: true }"
                   x-show="show"
                   x-transition
                   x-init="setTimeout(() => show = false, 2500)"
                   class="saved-flash"
                >✓ Enregistré</p>
            @endif
        </div>
    </form>
</section>
