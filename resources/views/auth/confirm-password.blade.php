<x-guest-layout>
    <div class="eyebrow eyebrow--accent" style="margin-bottom: 12px">Zone sensible</div>
    <h1>Confirmez votre mot de passe.</h1>
    <p class="lead">Cette action requiert une nouvelle confirmation pour des raisons de sécurité.</p>

    <form method="POST" action="{{ route('password.confirm') }}" class="form-stack" style="margin-top: 24px">
        @csrf

        <div>
            <x-input-label for="password" value="Mot de passe" />
            <x-text-input id="password" type="password" name="password" required autocomplete="current-password" autofocus style="margin-top: 6px" />
            <x-input-error :messages="$errors->get('password')" />
        </div>

        <div class="form-row">
            <span></span>
            <x-primary-button>Confirmer</x-primary-button>
        </div>
    </form>
</x-guest-layout>
