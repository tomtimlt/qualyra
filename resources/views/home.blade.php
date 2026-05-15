@extends('layouts.public')

@section('content')
<div class="home">

    {{-- Background light --}}
    <div class="home__light"></div>

    {{-- HERO --}}
    <section class="home__hero">
        <div class="home__hero-glow"></div>
        <div class="home__hero-grid">
            <div>
                <div class="eyebrow eyebrow--accent">AI Act · Règlement UE 2024/1689</div>
                <h1>L'audit,<br><em>posé.</em></h1>
                <p class="lead">
                    Qualyra est l'outil d'audit de conformité AI Act + RGPD pour les PME françaises.
                    Vous déclarez vos usages d'IA, nous évaluons leur niveau de risque selon le règlement européen,
                    et nous générons un rapport avec un plan d'action 1 mois / 6 mois / 1 an.
                </p>
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
                    <a class="btn btn--secondary btn--lg" href="#methode">Comment ça marche →</a>
                </div>
                <div class="home__hero-meta">
                    <span><b>4</b> niveaux de risque</span>
                    <span><b>+100</b> usages d'IA référencés</span>
                </div>
            </div>
            <div class="home__hero-mark">
                <canvas id="canvas"></canvas>
            </div>
        </div>
    </section>

    {{-- PRINCIPES --}}
    <section class="home__principles">
        <div class="section__head">
            <div>
                <div class="eyebrow eyebrow--accent">Posture</div>
                <h2>Quatre <em>principes.</em></h2>
            </div>
            <p class="section__head-aside">
                L'AI Act ne se résume pas à une checklist. Qualyra traduit la matrice réglementaire en posture opérationnelle pour les dirigeants de PME.
            </p>
        </div>
        <div class="principles">
            <div class="principle">
                <div class="principle__num">i.</div>
                <div class="principle__t">Lisible, pas juridique</div>
                <div class="principle__d">Le rapport est rédigé pour un dirigeant, pas un avocat. Aucune mention « sous réserve d'interprétation jurisprudentielle ».</div>
            </div>
            <div class="principle">
                <div class="principle__num">ii.</div>
                <div class="principle__t">Quatre niveaux, déterminés</div>
                <div class="principle__d">Inacceptable, haut, limité, minimal — la classification est calculée à partir de la matrice officielle, pas estimée à la main.</div>
            </div>
            <div class="principle">
                <div class="principle__num">iii.</div>
                <div class="principle__t">Plan d'action chiffré</div>
                <div class="principle__d">À 1 mois, 6 mois et 1 an. Chaque action a un responsable suggéré et un effort estimé. Pas de TODO sans propriétaire.</div>
            </div>
           
        </div>
    </section>

    {{-- METHODE --}}
    <section class="home__method" id="methode">
        <div class="section__head">
            <div>
                <div class="eyebrow eyebrow--accent">03 · Méthode</div>
                <h2>Trois <em>étapes.</em></h2>
            </div>
            <p class="section__head-aside">
                De la déclaration au rapport PDF, sans appel commercial, sans réunion de cadrage. L'audit prend en moyenne quinze minutes pour une PME de moins de cinquante salariés.
            </p>
        </div>

        <div class="method-grid">
            <div class="method-step">
                <div class="method-step__num">01</div>
                <div class="method-step__t">Déclarez vos usages IA</div>
                <div class="method-step__d">
                    Listez chaque outil ou système d'IA déployé : ChatGPT en production, scoring CV, génération marketing,
                    chatbot support, détection d'anomalies. Pour chacun, vous renseignez la finalité, les données traitées et le degré d'automatisation.
                </div>
            </div>
            <div class="method-step">
                <div class="method-step__num">02</div>
                <div class="method-step__t">Répondez au questionnaire ciblé</div>
                <div class="method-step__d">
                    Les questions s'adaptent au type d'IA déclaré. Les réponses sont sauvegardées au fur et à mesure :
                    vous pouvez interrompre l'audit et y revenir plus tard, ou faire valider chaque section par un collaborateur compétent.
                </div>
            </div>
            <div class="method-step">
                <div class="method-step__num">03</div>
                <div class="method-step__t">Recevez le rapport PDF</div>
                <div class="method-step__d">
                    Synthèse exécutive, niveau de risque par usage, obligations applicables, alertes RGPD, plan d'action 1 mois / 6 mois / 1 an,
                    checklist opérationnelle. PDF figé, daté, conservé dans l'historique de votre organisation.
                </div>
            </div>
        </div>
    </section>

    {{-- ENJEUX --}}
    <section class="home__why">
        <div class="why-grid">
            <div class="why-card why-card--ember">
                <div class="why-card__num">35 M€</div>
                <div class="why-card__t">ou 7 % du CA mondial</div>
                <div class="why-card__d">
                    Sanction maximale prévue par l'Article 99 §6 de l'AI Act pour les pratiques interdites (Article 5). Plafond
                    réduit pour les PME, mais l'arrêt immédiat reste obligatoire.
                </div>
            </div>
            <div class="why-card">
                <div class="why-card__num">02 août 2026</div>
                <div class="why-card__t">Article 6 (haut risque)</div>
                <div class="why-card__d">
                    Date d'entrée en vigueur des obligations principales pour les systèmes d'IA classés haut risque
                    (Annexe III). Documentation, journalisation, supervision humaine.
                </div>
            </div>
            <div class="why-card">
                <div class="why-card__num">2 août 2027</div>
                <div class="why-card__t">Pleine application</div>
                <div class="why-card__d">
                    Échéance complète du règlement. Avant cette date, les PME doivent avoir cartographié leurs usages
                    et lancé les analyses d'impact requises par les classifications.
                </div>
            </div>
        </div>
    </section>

    {{-- CTA FINALE --}}
    <section class="home__cta">
        <div class="home__cta-card">
            <div>
                <div class="home__cta-title">Prêt à <em>commencer</em> ?</div>
                <div class="home__cta-sub">L'inscription, la déclaration des usages et le questionnaire sont gratuits.</div>
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

    .home__hero { display: grid; grid-template-columns: 1fr 1.4fr; gap: 64px; align-items: center; min-height: 70vh; padding: 96px 0 80px; border-bottom: 1px solid var(--hairline); position: relative; }
    .home__hero-glow { position: absolute; top: 0; left: -10vw; right: -10vw; bottom: 0; background: radial-gradient(ellipse 60% 80% at 70% 50%, rgba(46, 95, 160, 0.06) 0%, transparent 60%); pointer-events: none; z-index: -1; }
    .home__hero-grid { display: contents; }
    .home__hero h1 { font-family: var(--font-display); font-size: 132px; line-height: 0.95; letter-spacing: -0.025em; margin: 24px 0 0; color: var(--text); }
    .home__hero h1 em { color: var(--accent); font-style: italic; }
    .home__hero .lead { margin-top: 32px; max-width: 56ch; }
    .home__hero-cta { margin-top: 40px; display: flex; gap: 12px; flex-wrap: wrap; }
    .home__hero-cta .btn { text-decoration: none; }
    .home__hero-meta { margin-top: 36px; display: flex; gap: 36px; flex-wrap: wrap; font-family: var(--font-mono); font-size: 11px; color: var(--text-dim); letter-spacing: 0.08em; text-transform: uppercase; }
    .home__hero-meta b { color: var(--text); font-family: var(--font-display); font-size: 18px; font-weight: 600; letter-spacing: -0.01em; text-transform: none; margin-right: 8px; }

    .home__hero-mark { display: flex; flex-direction: column; align-items: center; gap: 24px; position: relative; z-index: 1; }

    #canvas{
        width: 100%;
        height: auto;
        max-width: 800px;
        aspect-ratio: 1920 / 1000;
    }

    /* PRINCIPLES */
    .section__head { display: flex; justify-content: space-between; align-items: flex-end; gap: 48px; padding-bottom: 24px; border-bottom: 1px solid var(--hairline); margin-bottom: 32px; }
    .section__head h2 { font-family: var(--font-display); font-size: 56px; line-height: 1; letter-spacing: -0.02em; margin: 8px 0 0; }
    .section__head h2 em { color: var(--accent); font-style: italic; }
    .section__head-aside { font-size: 14px; color: var(--text-muted); max-width: 44ch; margin: 0; text-align: right; line-height: 1.55; }

    .home__principles { margin: 80px 0; }
    .principles { display: grid; grid-template-columns: repeat(3, 1fr); gap: 0; border: 1px solid var(--hairline); border-radius: var(--r-md); overflow: hidden; }
    .principle { padding: 32px 28px; border-right: 1px solid var(--hairline); }
    .principle:last-child { border-right: none; }
    .principle__num { font-family: var(--font-display); font-size: 32px; color: var(--accent); font-style: italic; line-height: 1; margin-bottom: 12px; }
    .principle__t { font-size: 15px; font-weight: 500; margin-bottom: 6px; color: var(--text); }
    .principle__d { font-size: 12px; color: var(--text-muted); line-height: 1.6; }

    /* METHOD */
    .home__method { margin: 80px 0; }
    .method-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; }
    .method-step { border: 1px solid var(--hairline); border-radius: var(--r-md); background: var(--surface); padding: 32px 28px; display: flex; flex-direction: column; gap: 14px; min-height: 240px; }
    .method-step__num { font-family: var(--font-mono); font-size: 11px; letter-spacing: 0.18em; color: var(--accent-soft); }
    .method-step__t { font-family: var(--font-display); font-size: 26px; line-height: 1.1; letter-spacing: -0.015em; color: var(--text); margin-top: auto; }
    .method-step__d { font-size: 13px; color: var(--text-muted); line-height: 1.6; }

    /* WHY */
    .home__why { margin: 80px 0; }
    .why-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; }
    .why-card { border: 1px solid var(--hairline); border-radius: var(--r-md); padding: 32px 28px; background: var(--surface); position: relative; overflow: hidden; }
    .why-card--ember { border-color: var(--risk-haut); background: var(--risk-haut-bg); }
    .why-card__num { font-family: var(--font-display); font-size: 44px; line-height: 1; letter-spacing: -0.02em; color: var(--text); margin-bottom: 8px; }
    .why-card--ember .why-card__num { color: var(--risk-haut); }
    .why-card__t { font-family: var(--font-mono); font-size: 11px; letter-spacing: 0.16em; color: var(--text-dim); text-transform: uppercase; margin-bottom: 16px; }
    .why-card__d { font-size: 13px; color: var(--text-muted); line-height: 1.6; }

    /* CTA */
    .home__cta { margin: 80px 0 40px; }
    .home__cta-card { border: 1px solid var(--hairline); border-radius: var(--r-md); padding: 48px 56px; display: flex; align-items: center; justify-content: space-between; gap: 32px; background: radial-gradient(ellipse at 90% 20%, color-mix(in oklab, var(--accent) 8%, transparent) 0%, transparent 60%), var(--surface); }
    .home__cta-title { font-family: var(--font-display); font-size: 48px; line-height: 1.05; letter-spacing: -0.02em; color: var(--text); }
    .home__cta-title em { color: var(--accent); font-style: italic; }
    .home__cta-sub { color: var(--text-muted); font-size: 14px; margin-top: 8px; max-width: 50ch; }
    .home__cta-actions { display: flex; gap: 12px; flex-shrink: 0; }
    .home__cta-actions .btn { text-decoration: none; }

    /* Responsive */
    @media (max-width: 960px) {
        .home { padding: 0 24px; }
        .home__hero { grid-template-columns: 1fr; padding: 48px 0; }
        .home__hero h1 { font-size: 88px; }
        .principles, .method-grid, .why-grid { grid-template-columns: 1fr; }
        .principle { border-right: none; border-bottom: 1px solid var(--hairline); }
        .principle:last-child { border-bottom: none; }
        .home__cta-card { flex-direction: column; align-items: flex-start; padding: 32px; }
        .home__cta-title { font-size: 36px; }
        .section__head { flex-direction: column; align-items: flex-start; gap: 16px; }
        .section__head-aside { text-align: left; }
        .section__head h2 { font-size: 40px; }
    }
</style>
@endsection
