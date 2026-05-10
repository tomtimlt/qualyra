<x-guest-layout>
    <div class="eyebrow eyebrow--accent" style="margin-bottom: 12px">Connexion</div>
    <h1>Bonjour.</h1>
    <p class="lead">Accédez à votre tableau de bord d'audit AI Act + RGPD.</p>

    <x-auth-session-status :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="form-stack" style="margin-top: 24px">
        @csrf

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" style="margin-top: 6px" />
            <x-input-error :messages="$errors->get('email')" />
        </div>

        <div>
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" type="password" name="password" required autocomplete="current-password" style="margin-top: 6px" />
            <x-input-error :messages="$errors->get('password')" />
        </div>

        <label for="remember_me" class="checkbox-row">
            <input id="remember_me" type="checkbox" name="remember">
            <span>Se souvenir de moi</span>
        </label>

        <div class="form-row">
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}">Mot de passe oublié ?</a>
            @else
                <span></span>
            @endif

            <x-primary-button>Se connecter</x-primary-button>
        </div>
    </form>

    <div style="margin-top: 24px; padding-top: 24px; border-top: 1px solid var(--hairline); font-size: 13px; color: var(--text-muted);">
        Pas encore de compte ?
        <a href="{{ route('register') }}" style="color: var(--accent-soft); text-decoration: none; margin-left: 6px;">Créer un compte →</a>
    </div>
</x-guest-layout>
