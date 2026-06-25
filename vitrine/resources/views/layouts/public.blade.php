<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Qualyra</title>
    <link rel="icon" type="image/png" href="/qualyra/brand/qualyra-mark-original.png">
    <link rel="stylesheet" href="/qualyra/css/qualyra.css">
    @if (config('vitrine.umami_src'))
    <script defer src="{{ config('vitrine.umami_src') }}" data-website-id="{{ config('vitrine.umami_website_id') }}"></script>
    @endif
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
    <style>
    body.public { background: var(--bg); color: var(--text); margin: 0; display: flex; flex-direction: column; height: 100vh; overflow: hidden; }

    .public__bar { flex-shrink: 0; position: sticky; top: 0; z-index: 10; backdrop-filter: blur(12px); background: color-mix(in oklab, var(--bg) 85%, transparent); border-bottom: 1px solid var(--hairline); }
    .public__bar-inner { max-width: 1280px; margin: 0 auto; padding: 12px 40px; display: flex; align-items: center; justify-content: space-between; gap: 32px; }
    .public__brand { display: flex; align-items: center; gap: 0; text-decoration: none; }
    .public__brand img { height: 64px; margin-right: -5px; }
    .brand-word { font-family: var(--font-display); font-size: 36px; line-height: 1; letter-spacing: -0.01em; color: var(--text); }
    .brand-word .dot { color: var(--accent); }
    .public__nav { display: flex; align-items: center; gap: 24px; }
    .public__nav a { color: var(--text-muted); text-decoration: none; font-size: 15px; transition: color var(--d-fast); }
    .public__nav a:hover { color: var(--text); }
    .public__nav .btn { text-decoration: none; padding: 12px 22px; font-size: 14px; height: auto; color: white; }
    .public__nav .btn--uiverse { padding: 0; height: 40px; min-width: 140px; }

    .public-scroll { flex: 1 1 auto; overflow-y: auto; overflow-x: hidden; scrollbar-width: none; -ms-overflow-style: none; transition: opacity 0.2s ease-out; }
    .public-scroll::-webkit-scrollbar { display: none; }

    .public__foot { border-top: 1px solid var(--hairline); margin-top: 96px; }
    .public__foot-inner { max-width: 1280px; margin: 0 auto; padding: 32px 40px; display: flex; justify-content: space-between; font-family: var(--font-mono); font-size: 11px; letter-spacing: 0.06em; color: var(--text-dim); text-transform: uppercase; }
    .public__foot-inner b { color: var(--text); font-weight: 500; }

    .lang-switch { display:inline-flex; align-items:center; height:32px; border:1px solid var(--hairline); border-radius:var(--r-sm); overflow:hidden; flex-shrink:0; }
    .lang-switch:hover { border-color:var(--hairline-strong); }
    .lang-switch__opt { display:inline-flex; align-items:center; justify-content:center; height:100%; padding:0 9px; font-family:var(--font-mono); font-size:11px; letter-spacing:0.08em; color:var(--text-dim); text-decoration:none; transition:all var(--d-fast) var(--ease-out); }
    .lang-switch__opt.is-active { background:var(--accent); color:#fff; }

    @media (max-width: 720px) {
      .public__bar-inner { padding: 16px 24px; }
    }
    </style>
    @stack('styles')
</head>
<body class="public">

<header class="public__bar">
  <div class="public__bar-inner">
    <a href="{{ route('home') }}" class="public__brand">
      <img src="/qualyra/brand/qualyra-mark-original.png" alt="">
      <span class="brand-word">Qualyra<span class="dot">.</span></span>
    </a>
    <nav class="public__nav">
      <span class="lang-switch">
        <a class="lang-switch__opt {{ app()->getLocale()==='fr' ? 'is-active' : '' }}" href="{{ route('locale','fr') }}">FR</a>
        <a class="lang-switch__opt {{ app()->getLocale()==='en' ? 'is-active' : '' }}" href="{{ route('locale','en') }}">EN</a>
      </span>
      <button id="theme-toggle" type="button" class="theme-toggle" aria-label="{{ __('ui.theme') }}">
        <svg class="icon-sun" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="16" height="16">
          <circle cx="12" cy="12" r="4"/>
          <path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M4.93 19.07l1.41-1.41M17.66 6.34l1.41-1.41"/>
        </svg>
        <svg class="icon-moon" style="display:none" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="16" height="16">
          <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>
        </svg>
      </button>
      <a class="btn btn--accent btn--sm btn--uiverse" href="{{ route('contact') }}">
        <div class="wrapper">
          <span>{{ __('cta.contact') }}</span>
          <div class="circle circle-12"></div><div class="circle circle-11"></div><div class="circle circle-10"></div>
          <div class="circle circle-9"></div><div class="circle circle-8"></div><div class="circle circle-7"></div>
          <div class="circle circle-6"></div><div class="circle circle-5"></div><div class="circle circle-4"></div>
          <div class="circle circle-3"></div><div class="circle circle-2"></div><div class="circle circle-1"></div>
        </div>
      </a>
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
      <span>&copy; {{ date('Y') }} &middot; Qualyra</span>
    </div>
  </footer>
</div>

@stack('scripts')
<script src="/js/site.js"></script>
</body>
</html>
