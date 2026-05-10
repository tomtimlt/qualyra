<x-guest-layout>
    <div class="eyebrow eyebrow--accent" style="margin-bottom: 12px">Inscription</div>
    <h1>Créer un compte.</h1>
    <p class="lead">Vous renseignerez votre PME et vos usages d'IA après l'inscription.</p>

    <form method="POST" action="{{ route('register') }}" class="form-stack" style="margin-top: 24px">
        @csrf

        <div>
            <x-input-label for="name" value="Nom" />
            <x-text-input id="name" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" style="margin-top: 6px" />
            <x-input-error :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" value="Email" />
            <x-text-input id="email" type="email" name="email" :value="old('email')" required autocomplete="username" style="margin-top: 6px" />
            <x-input-error :messages="$errors->get('email')" />
        </div>

        <div>
            <x-input-label for="password" value="Mot de passe" />
            <x-text-input id="password" type="password" name="password" required autocomplete="new-password" style="margin-top: 6px" />
            <x-input-error :messages="$errors->get('password')" />
        </div>

        <div>
            <x-input-label for="password_confirmation" value="Confirmer le mot de passe" />
            <x-text-input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" style="margin-top: 6px" />
            <x-input-error :messages="$errors->get('password_confirmation')" />
        </div>

        <div class="form-row">
            <a href="{{ route('login') }}">J'ai déjà un compte</a>
            <x-primary-button>S'inscrire</x-primary-button>
        </div>
    </form>
</x-guest-layout>
