<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Qualyra' }}</title>
    <link rel="icon" type="image/png" href="{{ asset('qualyra/brand/qualyra-mark-original.png') }}">
    <link rel="stylesheet" href="{{ asset('qualyra/css/qualyra.css') }}">
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
        <a href="{{ route('dashboard') }}" class="brand" aria-label="Qualyra — tableau de bord">
            <img src="{{ asset('qualyra/brand/qualyra-mark-original.png') }}" alt="">
            <div class="brand-word">Qualyra<span class="dot">.</span></div>
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
        </div>

        <div class="side__foot" x-data="{ open: false }" @click.outside="open = false" @keydown.escape.window="open = false">
            <button @click="open = !open" class="side__foot-btn" aria-label="Menu compte" type="button">
                <div class="avatar">{{ $initials }}</div>
                <div style="min-width: 0; text-align: left;">
                    <div class="side__foot-name">{{ $user?->name }}</div>
                    <div class="side__foot-meta">
                        @if ($organization?->siret)
                            SIRET · {{ $organization->siret }}
                        @else
                            {{ $user?->email }}
                        @endif
                    </div>
                </div>
            </button>

            <div x-show="open" style="display:none" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-1" class="side__dropdown">
                @if ($organization)
                    <a href="#" @click.prevent="open = false; $dispatch('open-modal', 'edit-organization')">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M3 21h18M5 21V7l8-4 8 4v14M9 9h.01M9 13h.01M9 17h.01M14 9h.01M14 13h.01M14 17h.01"/></svg>
                        {{ $organization->name }}
                    </a>
                @endif
                <a href="{{ route('profile.edit') }}" @click="open = false" class="{{ request()->routeIs('profile.*') ? 'is-active' : '' }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0116 0"/></svg>
                    Profil
                </a>
                <form method="POST" action="{{ route('logout') }}" @submit="open = false">
                    @csrf
                    <button type="submit">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4M16 17l5-5-5-5M21 12H9"/></svg>
                        Déconnexion
                    </button>
                </form>
            </div>
        </div>
    </aside>

    @if ($organization)
    <x-modal name="edit-organization" :show="$errors->hasAny(['name', 'siret', 'size', 'sector'])" focusable>
        <form method="POST" action="{{ route('organization.update') }}" class="modal-form" style="padding: 32px;">
            @csrf
            @method('PATCH')

            <div style="display: flex; flex-direction: column; gap: 24px;">
                <div>
                    <h2 style="font-family: var(--font-display); font-size: 22px; margin: 0;">Modifier l'organisation</h2>
                    <p style="font-size: 13px; color: var(--text-dim); margin: 8px 0 0;">{{ $organization->name }}</p>
                </div>

                <div class="form-grid">
                    <div>
                        <x-input-label for="modal-name" value="Nom *" />
                        <x-text-input id="modal-name" name="name" type="text" required autofocus
                                      :value="old('name', $organization->name)" style="margin-top: 6px" />
                        <x-input-error :messages="$errors->get('name')" />
                    </div>

                    <div>
                        <x-input-label for="modal-siret" value="SIRET" />
                        <x-text-input id="modal-siret" name="siret" type="text" maxlength="14"
                                      :value="old('siret', $organization->siret)" placeholder="14 chiffres"
                                      style="margin-top: 6px; font-family: var(--font-mono)" />
                        <x-input-error :messages="$errors->get('siret')" />
                    </div>
                </div>

                <div class="form-grid">
                    <div>
                        <x-input-label for="modal-size" value="Effectif *" />
                        <select id="modal-size" name="size" required class="input" style="margin-top: 6px">
                            <option value="">— Sélectionner —</option>
                            @foreach (['1-19', '20-49', '50-149', '150+'] as $sizeOption)
                                <option value="{{ $sizeOption }}" @selected(old('size', $organization->size) === $sizeOption)>
                                    {{ $sizeOption }} salariés
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('size')" />
                    </div>

                    <div>
                        <x-input-label for="modal-sector" value="Secteur d'activité" />
                        <x-text-input id="modal-sector" name="sector" type="text"
                                      :value="old('sector', $organization->sector)" placeholder="Ex : Industrie, Santé, RH"
                                      style="margin-top: 6px" />
                        <x-input-error :messages="$errors->get('sector')" />
                    </div>
                </div>

                <div style="display: flex; align-items: center; justify-content: flex-end; gap: 12px; padding-top: 20px; border-top: 1px solid var(--hairline);">
                    <x-secondary-button x-on:click="$dispatch('close')" type="button">Annuler</x-secondary-button>
                    <x-primary-button>Enregistrer</x-primary-button>
                </div>
            </div>
        </form>
    </x-modal>
    @endif

    <div class="app-main">
        @isset($header)
            <div class="app-topbar">
                <div class="crumb">
                    {{ $header }}
                </div>
            </div>
        @endisset

        <div class="q-scroll">
            <main class="app-content">
                {{ $slot }}
            </main>
        </div>
    </div>
</div>

<style>
    [x-cloak] { display: none !important; }
    body { background: var(--ink-1000); color: var(--text); overflow-x: hidden; }
    .app-shell { min-height: 100vh; display: grid; grid-template-columns: 240px 1fr; }

    ::-webkit-scrollbar { width: 8px; height: 8px; }
    ::-webkit-scrollbar-track { background: rgba(11, 15, 20, 0.3); border-radius: 4px; }
    ::-webkit-scrollbar-thumb { background: #2E5FA0; border-radius: 4px; }
    ::-webkit-scrollbar-thumb:hover { background: #6E92C7; }
    * { scrollbar-width: thin; scrollbar-color: #2E5FA0 rgba(11, 15, 20, 0.3); }

    .side { background: var(--ink-1000); border-right: 1px solid var(--hairline); padding: 24px 20px; display: flex; flex-direction: column; gap: 28px; position: sticky; top: 0; height: 100vh; z-index: 20; }
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

    .side__foot { margin-top: auto; position: relative; }
    .side__foot-btn { width: 100%; padding: 14px; border: 1px solid var(--hairline); border-radius: var(--r-md); display: flex; align-items: center; gap: 10px; background: transparent; color: inherit; cursor: pointer; transition: all var(--d-fast); text-align: left; }
    .side__foot-btn:hover { border-color: var(--ink-500); background: var(--ink-900); }
    .side__foot-btn:focus-visible { outline: 2px solid var(--accent); outline-offset: 2px; }
    .avatar { width: 32px; height: 32px; border-radius: 50%; background: linear-gradient(135deg, var(--stag-500), var(--ink-700)); display: flex; align-items: center; justify-content: center; font-size: 11px; font-weight: 600; color: var(--ink-50); flex-shrink: 0; }
    .side__foot-name { font-size: 12px; font-weight: 500; color: var(--text); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .side__foot-meta { font-family: var(--font-mono); font-size: 10px; color: var(--text-dim); letter-spacing: 0.04em; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .side__dropdown { position: absolute; bottom: 100%; left: 0; right: 0; margin-bottom: 8px; background: var(--ink-1000); border: 1px solid var(--hairline); border-radius: var(--r-md); padding: 6px; display: flex; flex-direction: column; gap: 2px; box-shadow: 0 -4px 24px rgba(0,0,0,0.3); z-index: 50; }
    .side__dropdown a, .side__dropdown button { display: flex; align-items: center; gap: 12px; padding: 9px 12px; border-radius: var(--r-sm); color: var(--ink-300); text-decoration: none; font-family: var(--font-sans); font-size: 13px; font-weight: 500; transition: all var(--d-fast) var(--ease-out); background: transparent; border: none; width: 100%; cursor: pointer; text-align: left; }
    .side__dropdown a:hover, .side__dropdown button:hover { background: var(--ink-900); color: var(--text); }
    .side__dropdown a svg, .side__dropdown button svg { width: 16px; height: 16px; opacity: 0.8; flex-shrink: 0; }
    .side__dropdown a.is-active { background: var(--ink-900); color: var(--text); box-shadow: inset 2px 0 0 var(--accent); padding-left: 14px; }

    .app-main { display: flex; flex-direction: column; min-width: 0; height: 100vh; }
    .app-topbar { flex-shrink: 0; padding: 18px 40px; border-bottom: 1px solid var(--hairline); position: sticky; top: 0; background: rgba(11, 15, 20, 0.85); backdrop-filter: blur(12px); z-index: 5; }
    .crumb { font-family: var(--font-mono); font-size: 11px; color: var(--text-dim); letter-spacing: 0.06em; text-transform: uppercase; }
    .crumb b { color: var(--text); font-weight: 500; }

    .app-content { padding: 40px; }

    /* Bandeau de statut Laravel session */
    .status-banner { padding: 14px 18px; border: 1px solid var(--hairline); border-radius: var(--r-md); font-size: 13px; margin-bottom: 24px; display: flex; gap: 10px; align-items: flex-start; }
    .status-banner--ok { border-left: 3px solid var(--risk-min); background: rgba(61, 110, 84, 0.08); }
    .status-banner--warn { border-left: 3px solid var(--risk-lim); background: rgba(160, 118, 38, 0.08); }
    .status-banner--info { border-left: 3px solid var(--accent); background: rgba(46, 95, 160, 0.08); }
</style>
</body>
</html>
