<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Cervus' }}</title>
    <link rel="icon" type="image/png" href="{{ asset('cervus/brand/cervus-mark-original.png') }}">
    <link rel="stylesheet" href="{{ asset('cervus/css/cervus.css') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
@php
    $user = auth()->user();
    $organization = $user?->organization;
    $initials = collect(explode(' ', $user?->name ?? '?'))
        ->take(2)
        ->map(fn ($w) => mb_strtoupper(mb_substr($w, 0, 1)))
        ->join('');
@endphp

<div class="app-shell">
    <aside class="side">
        <a href="{{ route('dashboard') }}" class="brand" aria-label="Cervus — tableau de bord">
            <img src="{{ asset('cervus/brand/cervus-mark-original.png') }}" alt="">
            <div class="brand-word">Cervus<span class="dot">.</span></div>
        </a>

        <div class="nav">
            <div class="nav__group">Audit</div>
            <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'is-active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M3 3h7v7H3zM14 3h7v7h-7zM14 14h7v7h-7zM3 14h7v7H3z"/></svg>
                Tableau de bord
            </a>
            @if ($organization)
                <a href="{{ route('usages.index') }}" class="{{ request()->routeIs('usages.*') ? 'is-active' : '' }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>
                    Mes usages IA
                    <span class="count">{{ str_pad((string) $organization->aiUsages()->count(), 2, '0', STR_PAD_LEFT) }}</span>
                </a>
                <a href="{{ route('reports.index') }}" class="{{ request()->routeIs('reports.*') ? 'is-active' : '' }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><path d="M14 2v6h6"/></svg>
                    Rapports
                </a>
            @endif

            <div class="nav__group">Compte</div>
            @if ($organization)
                <a href="#" style="pointer-events:none; opacity:.6">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M3 21h18M5 21V7l8-4 8 4v14M9 9h.01M9 13h.01M9 17h.01M14 9h.01M14 13h.01M14 17h.01"/></svg>
                    {{ $organization->name }}
                </a>
            @endif
            <a href="{{ route('profile.edit') }}" class="{{ request()->routeIs('profile.*') ? 'is-active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0116 0"/></svg>
                Profil
            </a>
            <form method="POST" action="{{ route('logout') }}" style="margin: 0">
                @csrf
                <button type="submit" class="nav__logout">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4M16 17l5-5-5-5M21 12H9"/></svg>
                    Déconnexion
                </button>
            </form>
        </div>

        <a href="{{ route('profile.edit') }}" class="side__foot" aria-label="Mon profil">
            <div class="avatar">{{ $initials }}</div>
            <div style="min-width: 0">
                <div class="side__foot-name">{{ $user?->name }}</div>
                <div class="side__foot-meta">
                    @if ($organization?->siret)
                        SIRET · {{ $organization->siret }}
                    @else
                        {{ $user?->email }}
                    @endif
                </div>
            </div>
        </a>
    </aside>

    <div class="app-main">
        @isset($header)
            <div class="app-topbar">
                <div class="crumb">
                    {{ $header }}
                </div>
            </div>
        @endisset

        <main class="app-content">
            {{ $slot }}
        </main>
    </div>
</div>

<style>
    body { background: var(--ink-1000); color: var(--text); }
    .app-shell { min-height: 100vh; display: grid; grid-template-columns: 240px 1fr; }

    .side { background: var(--ink-1000); border-right: 1px solid var(--hairline); padding: 24px 20px; display: flex; flex-direction: column; gap: 28px; position: sticky; top: 0; height: 100vh; }
    .brand { display: flex; align-items: center; gap: 10px; padding: 0 4px; text-decoration: none; transition: opacity var(--d-fast); }
    .brand:hover { opacity: 0.85; }
    .brand img { height: 28px; }
    .brand-word { font-family: var(--font-display); font-size: 22px; line-height: 1; letter-spacing: -0.01em; color: var(--text); }
    .brand-word .dot { color: var(--accent); }

    .nav { display: flex; flex-direction: column; gap: 2px; }
    .nav__group { font-family: var(--font-mono); font-size: 10px; letter-spacing: 0.16em; text-transform: uppercase; color: var(--text-dim); padding: 12px 12px 8px; }
    .nav a, .nav__logout {
        display: flex; align-items: center; gap: 12px;
        padding: 9px 12px; border-radius: var(--r-sm);
        color: var(--ink-300); text-decoration: none;
        font-family: var(--font-sans); font-size: 13px; font-weight: 500;
        transition: all var(--d-fast) var(--ease-out);
        background: transparent; border: none; width: 100%; cursor: pointer; text-align: left;
    }
    .nav a:hover, .nav__logout:hover { background: var(--ink-900); color: var(--text); }
    .nav a.is-active { background: var(--ink-900); color: var(--text); box-shadow: inset 2px 0 0 var(--accent); padding-left: 14px; }
    .nav a svg, .nav__logout svg { width: 16px; height: 16px; opacity: 0.8; flex-shrink: 0; }
    .nav a .count { margin-left: auto; font-family: var(--font-mono); font-size: 10px; color: var(--text-dim); }
    .nav a.is-active .count { color: var(--accent); }

    .side__foot { margin-top: auto; padding: 14px; border: 1px solid var(--hairline); border-radius: var(--r-md); display: flex; align-items: center; gap: 10px; text-decoration: none; color: inherit; transition: all var(--d-fast); cursor: pointer; }
    .side__foot:hover { border-color: var(--ink-500); background: var(--ink-900); }
    .avatar { width: 32px; height: 32px; border-radius: 50%; background: linear-gradient(135deg, var(--stag-500), var(--ink-700)); display: flex; align-items: center; justify-content: center; font-size: 11px; font-weight: 600; color: var(--ink-50); flex-shrink: 0; }
    .side__foot-name { font-size: 12px; font-weight: 500; color: var(--text); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .side__foot-meta { font-family: var(--font-mono); font-size: 10px; color: var(--text-dim); letter-spacing: 0.04em; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }

    .app-main { display: flex; flex-direction: column; min-width: 0; }
    .app-topbar { padding: 18px 40px; border-bottom: 1px solid var(--hairline); position: sticky; top: 0; background: rgba(11, 15, 20, 0.85); backdrop-filter: blur(12px); z-index: 5; }
    .crumb { font-family: var(--font-mono); font-size: 11px; color: var(--text-dim); letter-spacing: 0.06em; text-transform: uppercase; }
    .crumb b { color: var(--text); font-weight: 500; }

    .app-content { padding: 40px; flex: 1; }

    /* Bandeau de statut Laravel session */
    .status-banner { padding: 14px 18px; border: 1px solid var(--hairline); border-radius: var(--r-md); font-size: 13px; margin-bottom: 24px; display: flex; gap: 10px; align-items: flex-start; }
    .status-banner--ok { border-left: 3px solid var(--risk-min); background: rgba(61, 110, 84, 0.08); }
    .status-banner--warn { border-left: 3px solid var(--risk-lim); background: rgba(160, 118, 38, 0.08); }
    .status-banner--info { border-left: 3px solid var(--accent); background: rgba(46, 95, 160, 0.08); }
</style>
</body>
</html>
