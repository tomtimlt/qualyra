<section>
    <div class="form-section">
        <h4 class="form-section__title">Informations du compte</h4>
        <p class="form-section__sub">Le nom et l'email apparaissent en couverture des rapports PDF générés.</p>
    </div>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="form-stack-block">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="name" value="Nom" />
            <x-text-input id="name" name="name" type="text" :value="old('name', $user->name)" required autofocus autocomplete="name" style="margin-top: 6px" />
            <x-input-error :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" value="Email" />
            <x-text-input id="email" name="email" type="email" :value="old('email', $user->email)" required autocomplete="username" style="margin-top: 6px" />
            <x-input-error :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <p style="margin-top: 8px; font-size: 12px; color: var(--text-muted);">
                    Votre adresse email n'est pas vérifiée.
                    <button form="send-verification" type="submit" class="help-link">Renvoyer le lien de vérification</button>
                </p>

                @if (session('status') === 'verification-link-sent')
                    <p class="saved-flash" style="margin-top: 8px">Lien envoyé.</p>
                @endif
            @endif
        </div>

        <div class="form-actions-row">
            <x-primary-button>Enregistrer</x-primary-button>

            @if (session('status') === 'profile-updated')
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
