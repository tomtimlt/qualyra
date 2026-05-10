<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Cervus · Audit AI Act + RGPD' }}</title>
    <link rel="icon" type="image/png" href="{{ asset('cervus/brand/cervus-mark-original.png') }}">
    <link rel="stylesheet" href="{{ asset('cervus/css/cervus.css') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="public">

<header class="public__bar">
    <div class="public__bar-inner">
        <a href="{{ route('home') }}" class="public__brand">
            <img src="{{ asset('cervus/brand/cervus-mark-original.png') }}" alt="">
            <span class="brand-word">Cervus<span class="dot">.</span></span>
            <span class="public__tag">AI ACT · RGPD · COMPLIANCE</span>
        </a>
        <nav class="public__nav">
            @auth
                <a href="{{ route('dashboard') }}">Tableau de bord</a>
                <form method="POST" action="{{ route('logout') }}" style="margin:0;display:inline">
                    @csrf
                    <button type="submit" class="link-button">Déconnexion</button>
                </form>
            @else
                <a href="{{ route('login') }}">Connexion</a>
                <a href="{{ route('register') }}" class="btn btn--primary btn--sm">Commencer</a>
            @endauth
        </nav>
    </div>
</header>

<main>
    @yield('content')
</main>

<footer class="public__foot">
    <div class="public__foot-inner">
        <span>CERVUS · <b>v0.1</b> · CONFIDENTIEL</span>
        <span>© {{ date('Y') }} · {{ config('app.name', 'Cervus') }}</span>
    </div>
</footer>

<style>
    body.public { background: var(--ink-1000); color: var(--text); margin: 0; }

    .public__bar { position: sticky; top: 0; z-index: 10; backdrop-filter: blur(12px); background: rgba(11, 15, 20, 0.85); border-bottom: 1px solid var(--hairline); }
    .public__bar-inner { max-width: 1280px; margin: 0 auto; padding: 18px 40px; display: flex; align-items: center; justify-content: space-between; gap: 32px; }
    .public__brand { display: flex; align-items: center; gap: 12px; text-decoration: none; }
    .public__brand img { height: 28px; }
    .brand-word { font-family: var(--font-display); font-size: 22px; line-height: 1; letter-spacing: -0.01em; color: var(--text); }
    .brand-word .dot { color: var(--accent); }
    .public__tag { font-family: var(--font-mono); font-size: 10px; letter-spacing: 0.16em; color: var(--text-dim); text-transform: uppercase; margin-left: 12px; padding-left: 16px; border-left: 1px solid var(--hairline); }
    .public__nav { display: flex; align-items: center; gap: 20px; }
    .public__nav a { color: var(--text-muted); text-decoration: none; font-size: 13px; transition: color var(--d-fast); }
    .public__nav a:hover { color: var(--text); }
    .public__nav .btn { text-decoration: none; }
    .link-button { background: none; border: none; color: var(--text-muted); font-size: 13px; cursor: pointer; padding: 0; font-family: inherit; transition: color var(--d-fast); }
    .link-button:hover { color: var(--text); }

    .public__foot { border-top: 1px solid var(--hairline); margin-top: 96px; }
    .public__foot-inner { max-width: 1280px; margin: 0 auto; padding: 32px 40px; display: flex; justify-content: space-between; font-family: var(--font-mono); font-size: 11px; letter-spacing: 0.06em; color: var(--text-dim); text-transform: uppercase; }
    .public__foot-inner b { color: var(--text); font-weight: 500; }

    @media (max-width: 720px) {
        .public__tag { display: none; }
        .public__bar-inner { padding: 16px 24px; }
    }
</style>

</body>
</html>
