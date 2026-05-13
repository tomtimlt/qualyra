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
<body class="guest">

<div class="guest__bg"></div>

<div class="guest__shell" x-data="customScrollbar($el)" class="guest-scroll">
    <a href="{{ route('dashboard') }}" class="guest__brand">
        <img src="{{ asset('qualyra/brand/qualyra-mark-original.png') }}" alt="">
        <div class="brand-word">Qualyra<span class="dot">.</span></div>
    </a>

    <div class="guest__card">
        {{ $slot }}
    </div>

    <div class="guest__foot">
        <a href="{{ route('home') }}">← Retour à l'accueil</a>
        <span>AI Act · RGPD · v0.1</span>
    </div>

    <!-- Custom scrollbar track + thumb -->
    <div class="qs-track"
         x-ref="track"
         @mousedown="onTrackClick($event)"
         :class="{ 'is-visible': isVisible, 'is-dragging': isDragging }">
        <div class="qs-thumb"
             x-ref="thumb"
             @mousedown.stop="onThumbMouseDown($event)">
        </div>
    </div>
</div>

<style>
    body.guest { background: var(--ink-1000); color: var(--text); min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 40px 20px; position: relative; overflow-x: hidden; height: 100vh; overflow: hidden; }
    .guest__bg { position: fixed; inset: 0; pointer-events: none; z-index: 0; background: radial-gradient(ellipse 80% 60% at 50% 30%, rgba(46, 95, 160, 0.08) 0%, transparent 70%); }

    .guest-scroll {
        position: relative;
        width: 100%;
        max-width: 440px;
        overflow-y: auto;
        overflow-x: hidden;
        scrollbar-width: none; /* Firefox */
        -ms-overflow-style: none; /* IE/Edge */
        display: flex;
        flex-direction: column;
        gap: 32px;
    }
    .guest-scroll::-webkit-scrollbar {
        display: none; /* WebKit */
    }

    .guest__brand { display: flex; align-items: center; gap: 12px; text-decoration: none; justify-content: center; }
    .guest__brand img { height: 36px; }
    .brand-word { font-family: var(--font-display); font-size: 28px; line-height: 1; letter-spacing: -0.01em; color: var(--text); }
    .brand-word .dot { color: var(--accent); }

    .guest__card { background: var(--ink-950); border: 1px solid var(--hairline); border-radius: var(--r-md); padding: 40px 36px; }
    .guest__card h1 { font-family: var(--font-display); font-size: 28px; line-height: 1.1; letter-spacing: -0.015em; margin: 0 0 8px; color: var(--text); }
    .guest__card p.lead { color: var(--text-muted); font-size: 14px; margin: 0 0 28px; }

    .guest__foot { display: flex; justify-content: space-between; font-family: var(--font-mono); font-size: 10px; letter-spacing: 0.06em; color: var(--text-dim); text-transform: uppercase; }
    .guest__foot a { color: var(--text-dim); text-decoration: none; }
    .guest__foot a:hover { color: var(--text); }

    /* Form helpers shared with the rest of the system */
    .form-stack { display: flex; flex-direction: column; gap: 18px; }
    .form-row { display: flex; align-items: center; justify-content: space-between; gap: 12px; }
    .form-row a { font-size: 12px; color: var(--text-muted); text-decoration: none; border-bottom: 1px solid transparent; transition: border-color var(--d-fast); }
    .form-row a:hover { color: var(--text); border-bottom-color: var(--text-muted); }
    .checkbox-row { display: flex; align-items: center; gap: 8px; font-size: 13px; color: var(--text-muted); cursor: pointer; }
    .checkbox-row input { accent-color: var(--accent); }
    .link-button { background: none; border: none; color: var(--text-muted); font-size: 13px; cursor: pointer; padding: 0; font-family: inherit; transition: color var(--d-fast); text-decoration: underline; text-underline-offset: 3px; text-decoration-color: var(--hairline-strong); }
    .link-button:hover { color: var(--text); text-decoration-color: var(--text); }
</style>

</body>
</html>
