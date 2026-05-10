<x-guest-layout>
    <div class="eyebrow eyebrow--accent" style="margin-bottom: 12px">Réinitialisation</div>
    <h1>Nouveau mot de passe.</h1>
    <p class="lead">Choisissez un mot de passe que vous n'avez jamais utilisé ailleurs.</p>

    <form method="POST" action="{{ route('password.store') }}" class="form-stack" style="margin-top: 24px">
        @csrf

        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <div>
            <x-input-label for="email" value="Email" />
            <x-text-input id="email" type="email" name="email" :value="old('email', $request->email)" required autofocus autocomplete="username" style="margin-top: 6px" />
            <x-input-error :messages="$errors->get('email')" />
        </div>

        <div>
            <x-input-label for="password" value="Nouveau mot de passe" />
            <x-text-input id="password" type="password" name="password" required autocomplete="new-password" style="margin-top: 6px" />
            <x-input-error :messages="$errors->get('password')" />
        </div>

        <div>
            <x-input-label for="password_confirmation" value="Confirmer" />
            <x-text-input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" style="margin-top: 6px" />
            <x-input-error :messages="$errors->get('password_confirmation')" />
        </div>

        <div class="form-row">
            <span></span>
            <x-primary-button>Réinitialiser</x-primary-button>
        </div>
    </form>
</x-guest-layout>
