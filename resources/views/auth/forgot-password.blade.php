<x-guest-layout>
    <div class="eyebrow eyebrow--accent" style="margin-bottom: 12px">Mot de passe oublié</div>
    <h1>On vous renvoie un lien.</h1>
    <p class="lead">Saisissez l'email associé à votre compte. Nous vous enverrons un lien de réinitialisation.</p>

    <x-auth-session-status :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" class="form-stack" style="margin-top: 24px">
        @csrf

        <div>
            <x-input-label for="email" value="Email" />
            <x-text-input id="email" type="email" name="email" :value="old('email')" required autofocus style="margin-top: 6px" />
            <x-input-error :messages="$errors->get('email')" />
        </div>

        <div class="form-row">
            <a href="{{ route('login') }}">← Retour à la connexion</a>
            <x-primary-button>Envoyer le lien</x-primary-button>
        </div>
    </form>
</x-guest-layout>
