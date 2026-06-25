<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Qualyra &middot; Admin</title>
    <link rel="icon" type="image/png" href="/qualyra/brand/qualyra-mark-original.png">
    <link rel="stylesheet" href="/qualyra/css/qualyra.css">
    <style>
    body { background: var(--bg); color: var(--text); margin: 0; padding: 40px; font-family: var(--font-sans); }
    .admin__wrap { max-width: 960px; margin: 0 auto; }
    .admin__head { display: flex; justify-content: space-between; align-items: center; margin-bottom: 32px; }
    .admin__table { width: 100%; border-collapse: collapse; font-size: 13px; }
    .admin__table th, .admin__table td { text-align: left; padding: 10px 12px; border-bottom: 1px solid var(--hairline); }
    .admin__table th { font-family: var(--font-mono); font-size: 10px; letter-spacing: 0.1em; text-transform: uppercase; color: var(--text-dim); }
    .admin__table td { color: var(--text-muted); }
    .admin__table td:first-child { font-family: var(--font-mono); font-size: 11px; color: var(--text-dim); white-space: nowrap; }
    .admin__table td:nth-child(3) { max-width: 300px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .admin__pager { display: flex; gap: 8px; margin-top: 24px; }
    .admin__login { max-width: 360px; margin: 80px auto; display: flex; flex-direction: column; gap: 16px; }
    .admin__login h1 { font-family: var(--font-display); font-size: 36px; margin: 0 0 8px; }
    </style>
    @stack('styles')
</head>
<body>
    <div class="admin__wrap">
        @yield('content')
    </div>
    @stack('scripts')
</body>
</html>
