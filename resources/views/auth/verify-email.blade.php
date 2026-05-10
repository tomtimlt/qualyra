<x-guest-layout>
    <div class="eyebrow eyebrow--accent" style="margin-bottom: 12px">Vérification</div>
    <h1>On a vérifie votre email.</h1>
    <p class="lead">Cliquez sur le lien que nous venons de vous envoyer pour activer votre compte. Vérifiez aussi vos spams.</p>

    @if (session('status') == 'verification-link-sent')
        <div class="auth-session-status">Un nouveau lien vient d'être envoyé à l'adresse fournie lors de l'inscription.</div>
    @endif

    <div class="form-row" style="margin-top: 24px">
        <form method="POST" action="{{ route('logout') }}" style="margin: 0">
            @csrf
            <button type="submit" class="link-button">Déconnexion</button>
        </form>

        <form method="POST" action="{{ route('verification.send') }}" style="margin: 0">
            @csrf
            <x-primary-button>Renvoyer le lien</x-primary-button>
        </form>
    </div>
</x-guest-layout>
