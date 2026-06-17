<!DOCTYPE html>
@php
    $themePref = auth()->user()?->theme_preference ?? 'system';
@endphp
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
      @if (in_array($themePref, ['light', 'dark'])) data-theme="{{ $themePref }}" @endif>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Qualyra · Audit AI Act + RGPD' }}</title>
    <link rel="icon" type="image/png" href="{{ asset('qualyra/brand/qualyra-mark-original.png') }}">
    <link rel="stylesheet" href="{{ asset('qualyra/css/qualyra.css') }}">
    <script>
    (function() {
      var html = document.documentElement;
      if (html.hasAttribute('data-theme')) return;
      var stored = localStorage.getItem('qualyra-theme');
      if (stored === 'light' || stored === 'dark') {
        html.setAttribute('data-theme', stored);
      } else {
        var prefersLight = window.matchMedia('(prefers-color-scheme: light)').matches;
        html.setAttribute('data-theme', prefersLight ? 'light' : 'dark');
      }
    })();
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="public">

<header class="public__bar">
  <div class="public__bar-inner">
    <a href="{{ route('home') }}" class="public__brand">
      <img src="{{ asset('qualyra/brand/qualyra-mark-original.png') }}" alt="">
      <span class="brand-word">Qualyra<span class="dot">.</span></span>
    </a>
    <nav class="public__nav">
      <x-theme-toggle />
      @auth
        <a href="{{ route('dashboard') }}">Tableau de bord</a>
        <form method="POST" action="{{ route('logout') }}" style="margin:0;display:inline">
          @csrf
          <button type="submit" class="link-button">Déconnexion</button>
        </form>
      @else
        <a href="{{ route('login') }}">Connexion</a>
        <a href="{{ route('register') }}" class="btn btn--accent btn--sm btn--uiverse">
          <div class="wrapper">
            <span>Commencer</span>
            <div class="circle circle-12"></div>
            <div class="circle circle-11"></div>
            <div class="circle circle-10"></div>
            <div class="circle circle-9"></div>
            <div class="circle circle-8"></div>
            <div class="circle circle-7"></div>
            <div class="circle circle-6"></div>
            <div class="circle circle-5"></div>
            <div class="circle circle-4"></div>
            <div class="circle circle-3"></div>
            <div class="circle circle-2"></div>
            <div class="circle circle-1"></div>
          </div>
        </a>
      @endauth
    </nav>
  </div>
</header>

<div class="public-scroll">
  <main>
      @yield('content')
  </main>

  <footer class="public__foot">
    <div class="public__foot-inner">
      <span>QUALYRA</span>
      <span>© {{ date('Y') }} · {{ config('app.name', 'Qualyra') }}</span>
    </div>
  </footer>
</div>

<style>
  body.public { background: var(--bg); color: var(--text); margin: 0; display: flex; flex-direction: column; height: 100vh; overflow: hidden; }

  .public__bar { flex-shrink: 0; position: sticky; top: 0; z-index: 10; backdrop-filter: blur(12px); background: color-mix(in oklab, var(--bg) 85%, transparent); border-bottom: 1px solid var(--hairline); }
  .public__bar-inner { max-width: 1280px; margin: 0 auto; padding: 12px 40px; display: flex; align-items: center; justify-content: space-between; gap: 32px; }
  .public__brand { display: flex; align-items: center; gap: 0; text-decoration: none; }
  .public__brand img { height: 64px; margin-right: -5px; }
  .brand-word { font-family: var(--font-display); font-size: 36px; line-height: 1; letter-spacing: -0.01em; color: var(--text); }
  .brand-word .dot { color: var(--accent); }
  .public__tag { display: none; }
  .public__nav { display: flex; align-items: center; gap: 24px; }
  .public__nav a { color: var(--text-muted); text-decoration: none; font-size: 15px; transition: color var(--d-fast); }
  .public__nav a:hover { color: var(--text); }
  .public__nav .btn { text-decoration: none; padding: 12px 22px; font-size: 14px; height: auto; color: white; }
  .public__nav .btn--uiverse { padding: 0; height: 40px; min-width: 140px; }
  .link-button { background: none; border: none; color: var(--text-muted); font-size: 15px; cursor: pointer; padding: 0; font-family: inherit; transition: color var(--d-fast); }
  .link-button:hover { color: var(--text); }

  .public-scroll { flex: 1 1 auto; overflow-y: auto; overflow-x: hidden; scrollbar-width: none; -ms-overflow-style: none; }
  .public-scroll::-webkit-scrollbar { display: none; }

  .public__foot { border-top: 1px solid var(--hairline); margin-top: 96px; }
  .public__foot-inner { max-width: 1280px; margin: 0 auto; padding: 32px 40px; display: flex; justify-content: space-between; font-family: var(--font-mono); font-size: 11px; letter-spacing: 0.06em; color: var(--text-dim); text-transform: uppercase; }
  .public__foot-inner b { color: var(--text); font-weight: 500; }

  @media (max-width: 720px) {
    .public__bar-inner { padding: 16px 24px; }
  }
</style>

</body>
</html>
