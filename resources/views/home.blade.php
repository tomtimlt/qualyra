@extends('layouts.public')

@section('content')
<div class="home">

    {{-- Background light --}}
    <div class="home__light"></div>

    {{-- HERO — resserré, avec mockup navigateur --}}
    <section class="home__hero">
        <div class="home__hero-glow"></div>
        <div>
            <h1>Votre entreprise utilise déjà l'IA.<br><em>Le règlement aussi.</em></h1>
            <p class="lead">
                Qualyra classe chacun de vos usages d'IA selon une grille construite à partir du règlement européen, et vous remet un rapport avec un plan d'action à 1 mois, 6 mois et 1 an. Rédigé pour un dirigeant, pas pour un avocat.
            </p>
            <p class="home__hero-date">Les obligations pour les systèmes à haut risque s'appliquent à partir du <b>2&nbsp;août&nbsp;2026</b>.</p>
            <div class="home__hero-cta">
                <a class="btn btn--accent btn--lg btn--uiverse" href="{{ route('register') }}">
                    <div class="wrapper">
                        <span>Commencer mon audit</span>
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
                <a class="btn btn--secondary btn--lg" href="#produit">Voir le rapport &rarr;</a>
            </div>
            <div class="home__hero-meta">
                <span><b>4</b> niveaux de risque</span>
                <span><b>30</b> min</span>
                <span>Rapport PDF <b>personnalisé</b></span>
            </div>
        </div>
        <div class="home__hero-mark">
            <canvas id="canvas"></canvas>
        </div>
    </section>

    {{-- BANDE DE TENSION RÈGLEMENTAIRE — une ligne, registre CNIL --}}
    <section class="home__tension">
        <p class="tension__text">Les pratiques interdites exposent à une amende pouvant atteindre <b>35&nbsp;M€</b> ou <b>7&nbsp;% du chiffre d'affaires mondial</b> <span class="tension__ref">(article&nbsp;99 du règlement)</span>.</p>
    </section>

    {{-- LE PRODUIT — cœur de la refonte, ancre #produit --}}
    <section class="home__produit" id="produit">
        <div class="section__head">
            <div>
                <div class="eyebrow eyebrow--accent">Le livrable</div>
                <h2>Une demi-heure. <em>Pas un cabinet, pas un rendez-vous.</em></h2>
            </div>
            <p class="section__head-aside">
                Vous déclarez vos usages, le moteur applique sa grille d'analyse, le rapport tombe. Tout ce que vous voyez ci-dessous est généré par l'outil, pas par un consultant.
            </p>
        </div>

        {{-- Rangée A — Dashboard --}}
        <div class="produit-row">
            <div class="produit-row__visual">
                <div class="browser browser--flat">
                    <div class="browser__bar">
                        <span class="browser__dot browser__dot--red"></span>
                        <span class="browser__dot browser__dot--yellow"></span>
                        <span class="browser__dot browser__dot--green"></span>
                        <span class="browser__url">mon-tableau-de-bord.qualyra.app</span>
                    </div>
                    <div class="browser__body">
                        <img class="preview-img preview-img--dark" src="{{ asset('qualyra/img/preview-dashboard-dark.png') }}" alt="Dashboard Qualyra — dark">
                        <img class="preview-img preview-img--light" src="{{ asset('qualyra/img/preview-dashboard-light.png') }}" alt="Dashboard Qualyra — light">
                    </div>
                </div>
            </div>
            <div class="produit-row__text">
                <div class="produit-row__eyebrow">Tableau de bord</div>
                <div class="produit-row__t">Chaque usage IA, <em>classé.</em></div>
                <div class="produit-row__d">
                    Inacceptable, haut, limité, minimal — la classification est calculée par une grille construite à partir du règlement, pas estimée à la main. Votre tableau de bord montre où vous en êtes, usage par usage.
                </div>
            </div>
        </div>

        {{-- Rangée B — Rapport PDF --}}
        <div class="produit-row produit-row--reverse">
            <div class="produit-row__visual">
                <div class="report-spread">
                    <div class="report-spread__page report-spread__page--back">
                        <img src="{{ asset('qualyra/img/preview-report-3.png') }}" alt="Page 3 du rapport">
                    </div>
                    <div class="report-spread__page report-spread__page--mid">
                        <img src="{{ asset('qualyra/img/preview-report-2.png') }}" alt="Page 2 du rapport">
                    </div>
                    <div class="report-spread__page report-spread__page--front">
                        <img src="{{ asset('qualyra/img/preview-report-1.png') }}" alt="Page 1 du rapport">
                    </div>
                </div>
            </div>
            <div class="produit-row__text">
                <div class="produit-row__eyebrow">Rapport PDF</div>
                <div class="produit-row__t">Un rapport que vous pouvez <br><em>poser sur la table.</em></div>
                <div class="produit-row__d">
                    Synthèse exécutive, obligations applicables, alertes données personnelles, plan d'action <b>1 mois / 6 mois / 1 an</b> avec responsable suggéré et effort estimé. PDF figé, daté, conservé dans l'historique de votre organisation.
                </div>
            </div>
        </div>
    </section>

    {{-- MÉTHODE — timeline horizontale compressée --}}
    <section class="home__method" id="methode">
        <div class="section__head">
            <div>
                <div class="eyebrow eyebrow--accent">Méthode</div>
                <h2>Trois <em>étapes.</em></h2>
            </div>
        </div>

        <div class="method-timeline">
            <div class="method-timeline__step">
                <div class="method-timeline__num">01</div>
                <div class="method-timeline__t">Déclarez vos usages IA</div>
                <div class="method-timeline__d">ChatGPT en production, scoring CV, chatbot support : pour chacun, la finalité, les données traitées, le degré d'automatisation.</div>
            </div>
            <div class="method-timeline__connector">
                <div class="method-timeline__arrow">&rarr;</div>
            </div>
            <div class="method-timeline__step">
                <div class="method-timeline__num">02</div>
                <div class="method-timeline__t">Répondez au questionnaire ciblé</div>
                <div class="method-timeline__d">Les questions s'adaptent au type d'IA déclaré. Vous pouvez interrompre et reprendre à tout moment.</div>
            </div>
            <div class="method-timeline__connector">
                <div class="method-timeline__arrow">&rarr;</div>
            </div>
            <div class="method-timeline__step">
                <div class="method-timeline__num">03</div>
                <div class="method-timeline__t">Recevez le rapport</div>
                <div class="method-timeline__d">Niveau de risque par usage, obligations, plan d'action. En PDF, daté, prêt pour votre board.</div>
            </div>
        </div>
    </section>

    {{-- POSTURE — 2×2 (3 principes + carte échéance) --}}
    <section class="home__posture">
        <div class="section__head">
            <div>
                <div class="eyebrow eyebrow--accent">Posture</div>
                <h2>Trois principes. <em>Une échéance.</em></h2>
            </div>
        </div>
        <div class="posture-grid">
            <div class="posture-card">
                <div class="posture-card__num">i.</div>
                <div class="posture-card__t">Lisible, pas juridique</div>
                <div class="posture-card__d">Aucune mention « sous réserve d'interprétation jurisprudentielle ». Si une phrase du rapport ne vous sert pas à décider, elle n'y est pas.</div>
            </div>
            <div class="posture-card posture-card--ember">
                <div class="posture-card__num">2 août 2027</div>
                <div class="posture-card__t">Pleine application</div>
                <div class="posture-card__d">Avant cette date, votre cartographie des usages et vos analyses d'impact doivent être faites. Commencer maintenant, c'est les faire au calme.</div>
            </div>
            <div class="posture-card">
                <div class="posture-card__num">ii.</div>
                <div class="posture-card__t">Déterminé, pas estimé</div>
                <div class="posture-card__d">La classification sort d'une grille de critères, pas d'un avis. Deux entreprises identiques obtiennent le même résultat.</div>
            </div>
            <div class="posture-card">
                <div class="posture-card__num">iii.</div>
                <div class="posture-card__t">Chiffré, pas vague</div>
                <div class="posture-card__d">Chaque action a un responsable suggéré et un effort estimé. Pas de TODO sans propriétaire.</div>
            </div>
        </div>
    </section>

    {{-- CTA FINALE --}}
    <section class="home__cta">
        <div class="home__cta-card">
            <div>
                <div class="home__cta-title">Le règlement n'attendra pas. <em>Votre audit peut commencer maintenant.</em></div>
                <div class="home__cta-sub">Gratuit. Une demi-heure. Un rapport daté dans votre historique.</div>
            </div>
            <div class="home__cta-actions">
                <a class="btn btn--accent btn--lg btn--uiverse" href="{{ route('register') }}">
                    <div class="wrapper">
                        <span>Créer mon compte</span>
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
                <a class="btn btn--secondary btn--lg" href="{{ route('login') }}">J'ai déjà un compte</a>
            </div>
        </div>
    </section>

</div>

<style>
    html, body{
        background: var(--bg);
        overflow-x: hidden;
    }

    .home { max-width: 1280px; margin: 0 auto; padding: 0 40px; }

    .home__light {
        position: fixed;
        inset: 0;
        pointer-events: none;
        z-index: 0;
        background: radial-gradient(circle 55% at 70% 40%, rgba(46, 95, 160, 0.25) 0%, rgba(46, 95, 160, 0.04) 40%, transparent 70%);
        animation: pulse 4s ease-in-out infinite;
    }

    @keyframes pulse {
        0%, 100% { opacity: 0.3; }
        50% { opacity: 1; }
    }

    /* ── HERO ── */
    .home__hero { display: grid; grid-template-columns: 1fr 1.4fr; gap: 64px; align-items: center; min-height: 70vh; padding: 96px 0 48px; position: relative; }
    .home__hero-glow { position: absolute; top: 0; left: -10vw; right: -10vw; bottom: 0; background: radial-gradient(ellipse 60% 80% at 70% 50%, rgba(46, 95, 160, 0.06) 0%, transparent 60%); pointer-events: none; z-index: -1; }
    .home__hero h1 { font-family: var(--font-display); font-size: 96px; line-height: 0.95; letter-spacing: -0.025em; margin: 24px 0 0; color: var(--text); }
    .home__hero h1 em { color: var(--accent); font-style: italic; }
    .home__hero .lead { margin-top: 24px; max-width: 52ch; }
    .home__hero-cta { margin-top: 32px; display: flex; gap: 12px; flex-wrap: wrap; }
    .home__hero-cta .btn { text-decoration: none; }
    .home__hero-meta { margin-top: 28px; display: flex; gap: 36px; flex-wrap: wrap; font-family: var(--font-mono); font-size: 11px; color: var(--text-dim); letter-spacing: 0.08em; text-transform: uppercase; }
    .home__hero-meta b { color: var(--text); font-family: var(--font-display); font-size: 18px; font-weight: 600; letter-spacing: -0.01em; text-transform: none; margin-right: 4px; }

    .home__hero-date { margin-top: 24px; font-size: 13px; color: var(--text-muted); line-height: 1.5; }
    .home__hero-date b { color: var(--text); font-weight: 500; }

    .home__hero-mark { display: flex; flex-direction: column; align-items: center; align-self: start; margin-top: 40px; position: relative; z-index: 1; }

    #canvas{
        width: 100%;
        height: auto;
        max-width: 800px;
        aspect-ratio: 1920 / 1000;
    }

    /* Browser frame */
    .browser {
        width: 100%;
        border: 1px solid var(--hairline);
        border-radius: var(--r-lg);
        overflow: hidden;
        background: var(--surface);
        transform: perspective(1200px) rotateY(-2deg) rotateX(1deg);
        box-shadow: 0 24px 60px -20px rgba(0,0,0,0.5), 0 0 0 0 transparent;
        transition: box-shadow var(--d-base) var(--ease-out);
        position: relative;
    }
    .browser::after {
        content: '';
        position: absolute;
        inset: -1px;
        border-radius: var(--r-lg);
        pointer-events: none;
        box-shadow: 0 0 30px rgba(46, 95, 160, 0.15), 0 0 0 1px rgba(46, 95, 160, 0.08);
        opacity: 0;
        transition: opacity var(--d-base);
    }
    .browser:hover::after { opacity: 1; }
    .browser--flat { transform: none; }
    .browser__bar {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 12px 16px;
        background: var(--surface-2);
        border-bottom: 1px solid var(--hairline);
    }
    .browser__dot { width: 10px; height: 10px; border-radius: 50%; flex-shrink: 0; }
    .browser__dot--red { background: #FF5F56; }
    .browser__dot--yellow { background: #FFBD2E; }
    .browser__dot--green { background: #27C93F; }
    .browser__url {
        margin-left: 12px;
        font-family: var(--font-mono);
        font-size: 10px;
        color: var(--text-dim);
        background: var(--bg);
        padding: 4px 12px;
        border-radius: var(--r-sm);
        flex: 1;
        text-align: center;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        letter-spacing: 0.04em;
    }
    .browser__body {
        position: relative;
        width: 100%;
        aspect-ratio: 1440 / 840;
        overflow: hidden;
        background: var(--bg);
    }
    .browser__body img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    .preview-img { width: 100%; height: auto; display: block; }
    [data-theme="light"] .preview-img--dark { display: none; }
    [data-theme="dark"] .preview-img--light { display: none; }

    /* ── BANDE DE TENSION ── */
    .home__tension {
        padding: 40px 0 8px;
    }
    .tension__text {
        margin: 0;
        font-size: 19px;
        color: var(--text-muted);
        line-height: 1.55;
        max-width: 58ch;
    }
    .tension__text b { color: var(--risk-haut); font-weight: 500; }
    .tension__ref { font-size: 14px; color: var(--text-dim); }

    /* ── PRODUIT ── */
    .home__produit { margin: 80px 0; }
    .produit-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 64px;
        align-items: center;
        margin-bottom: 80px;
    }
    .produit-row:last-child { margin-bottom: 0; }
    .produit-row--reverse { direction: rtl; }
    .produit-row--reverse > * { direction: ltr; }
    .produit-row__visual { position: relative; }
    .produit-row__text { display: flex; flex-direction: column; gap: 16px; }
    .produit-row__eyebrow {
        font-family: var(--font-mono);
        font-size: 11px;
        letter-spacing: 0.18em;
        color: var(--accent-soft);
        text-transform: uppercase;
    }
    .produit-row__t {
        font-family: var(--font-display);
        font-size: 40px;
        line-height: 1.05;
        letter-spacing: -0.02em;
        color: var(--text);
    }
    .produit-row__t em { color: var(--accent); font-style: italic; }
    .produit-row__d {
        font-size: 14px;
        color: var(--text-muted);
        line-height: 1.65;
        max-width: 48ch;
    }
    .produit-row__d b { color: var(--text); font-weight: 500; }

    /* Report éventail */
    .report-spread {
        position: relative;
        width: 100%;
        aspect-ratio: 4 / 3;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .report-spread__page {
        position: absolute;
        width: 75%;
        border-radius: var(--r-md);
        overflow: hidden;
        border: 1px solid var(--hairline);
        box-shadow: var(--shadow-2);
        transition: transform var(--d-base) var(--ease-out);
    }
    .report-spread__page img {
        width: 100%;
        height: auto;
        display: block;
    }
    .report-spread__page--back {
        transform: rotate(-3deg) translateX(8%) translateY(4%);
        z-index: 1;
        opacity: 0.5;
    }
    .report-spread__page--mid {
        transform: rotate(-1deg) translateX(3%) translateY(0%);
        z-index: 2;
        opacity: 0.75;
    }
    .report-spread__page--front {
        transform: rotate(1deg);
        z-index: 3;
    }

    /* ── SECTION HEAD (shared) ── */
    .section__head { display: flex; justify-content: space-between; align-items: flex-end; gap: 48px; padding-bottom: 24px; border-bottom: 1px solid var(--hairline); margin-bottom: 40px; }
    .section__head h2 { font-family: var(--font-display); font-size: 56px; line-height: 1; letter-spacing: -0.02em; margin: 8px 0 0; }
    .section__head h2 em { color: var(--accent); font-style: italic; }
    .section__head-aside { font-size: 14px; color: var(--text-muted); max-width: 44ch; margin: 0; text-align: right; line-height: 1.55; }

    /* ── METHOD TIMELINE ── */
    .home__method { margin: 80px 0; }
    .method-timeline {
        display: grid;
        grid-template-columns: 1fr auto 1fr auto 1fr;
        gap: 0;
        align-items: start;
    }
    .method-timeline__step {
        border: 1px solid var(--hairline);
        border-radius: var(--r-md);
        background: var(--surface);
        padding: 28px 24px;
        display: flex;
        flex-direction: column;
        gap: 10px;
    }
    .method-timeline__num {
        font-family: var(--font-mono);
        font-size: 11px;
        letter-spacing: 0.18em;
        color: var(--accent-soft);
    }
    .method-timeline__t {
        font-family: var(--font-display);
        font-size: 22px;
        line-height: 1.1;
        letter-spacing: -0.015em;
        color: var(--text);
    }
    .method-timeline__d {
        font-size: 13px;
        color: var(--text-muted);
        line-height: 1.6;
    }
    .method-timeline__connector {
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 28px 12px;
    }
    .method-timeline__arrow {
        font-family: var(--font-display);
        font-size: 32px;
        color: var(--accent-soft);
        line-height: 1;
        opacity: 0.5;
    }

    /* ── POSTURE 2×2 ── */
    .home__posture { margin: 80px 0; }
    .posture-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 0;
        border: 1px solid var(--hairline);
        border-radius: var(--r-md);
        overflow: hidden;
    }
    .posture-card {
        padding: 32px 28px;
        border-right: 1px solid var(--hairline);
        border-bottom: 1px solid var(--hairline);
    }
    .posture-card:nth-child(2n) { border-right: none; }
    .posture-card:nth-last-child(-n+2) { border-bottom: none; }
    .posture-card--ember {
        border-color: var(--risk-haut);
        background: var(--risk-haut-bg);
    }
    .posture-card__num {
        font-family: var(--font-display);
        font-size: 32px;
        color: var(--accent);
        font-style: italic;
        line-height: 1;
        margin-bottom: 12px;
    }
    .posture-card--ember .posture-card__num {
        color: var(--risk-haut);
        font-size: 44px;
        font-style: normal;
    }
    .posture-card__t {
        font-size: 15px;
        font-weight: 500;
        margin-bottom: 6px;
        color: var(--text);
    }
    .posture-card--ember .posture-card__t { font-family: var(--font-mono); font-size: 11px; letter-spacing: 0.16em; color: var(--text-dim); text-transform: uppercase; }
    .posture-card__d {
        font-size: 12px;
        color: var(--text-muted);
        line-height: 1.6;
    }

    /* ── CTA ── */
    .home__cta { margin: 80px 0 40px; }
    .home__cta-card {
        border: 1px solid var(--hairline);
        border-radius: var(--r-md);
        padding: 48px 56px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 32px;
        background: radial-gradient(ellipse at 90% 20%, color-mix(in oklab, var(--accent) 8%, transparent) 0%, transparent 60%), var(--surface);
    }
    .home__cta-title { font-family: var(--font-display); font-size: 48px; line-height: 1.05; letter-spacing: -0.02em; color: var(--text); }
    .home__cta-title em { color: var(--accent); font-style: italic; }
    .home__cta-sub { color: var(--text-muted); font-size: 14px; margin-top: 8px; max-width: 50ch; }
    .home__cta-actions { display: flex; gap: 12px; flex-shrink: 0; }
    .home__cta-actions .btn { text-decoration: none; }

    /* ── RESPONSIVE ── */
    @media (max-width: 960px) {
        .home { padding: 0 24px; }
        .home__hero { grid-template-columns: 1fr; padding: 48px 0; min-height: auto; }
        .home__hero h1 { font-size: 68px; }
        .home__hero-mark { margin-top: 40px; }
        .browser { transform: none; }
        .browser__body { aspect-ratio: 1440 / 840; }

        .tension__text { font-size: 13px; }
        .home__hero-date { font-size: 12px; }

        .section__head { flex-direction: column; align-items: flex-start; gap: 16px; margin-bottom: 28px; }
        .section__head-aside { text-align: left; }
        .section__head h2 { font-size: 40px; }

        .produit-row { grid-template-columns: 1fr; gap: 32px; margin-bottom: 56px; }
        .produit-row--reverse { direction: ltr; }
        .produit-row__t { font-size: 32px; }
        .produit-row__visual { order: -1; }
        .report-spread { aspect-ratio: 3 / 2; }
        .report-spread__page { width: 85%; }
        .report-spread__page--back { transform: rotate(-2deg) translateX(5%) translateY(3%); }
        .report-spread__page--mid { transform: rotate(-0.5deg) translateX(2%) translateY(0%); }

        .method-timeline { grid-template-columns: 1fr; gap: 16px; }
        .method-timeline__connector { padding: 0; transform: rotate(90deg); }

        .posture-grid { grid-template-columns: 1fr; }
        .posture-card { border-right: none; border-bottom: 1px solid var(--hairline); }
        .posture-card:nth-child(2n) { border-right: none; }
        .posture-card:last-child { border-bottom: none; }

        .home__cta-card { flex-direction: column; align-items: flex-start; padding: 32px; }
        .home__cta-title { font-size: 36px; }
    }
</style>
@endsection
