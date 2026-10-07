<p align="center">
  <img src="public/qualyra/brand/qualyra-mark-original.png" alt="Qualyra" width="120">
</p>

<h1 align="center">Qualyra</h1>

<p align="center"><strong>English</strong> · <a href="README.fr.md">Français</a></p>

<p align="center">
  <strong>Your company already uses AI. So does EU regulation.</strong><br>
  An open-source EU AI Act + GDPR compliance audit for small businesses that have no DPO, no CISO, and no €30k for a consulting firm.
</p>

<p align="center">
  <a href="https://github.com/tomtimlt/qualyra/actions/workflows/tests.yml"><img alt="Tests" src="https://img.shields.io/github/actions/workflow/status/tomtimlt/qualyra/tests.yml?branch=main&style=flat-square&label=tests"></a>
  <a href="LICENSE"><img alt="License: AGPL-3.0" src="https://img.shields.io/badge/license-AGPL--3.0-blue?style=flat-square"></a>
  <img alt="Open source" src="https://img.shields.io/badge/open%20source-yes-2ea44f?style=flat-square">
  <a href="https://www.php.net/releases/8.4/"><img alt="PHP" src="https://img.shields.io/badge/PHP-8.4-777BB4?style=flat-square&logo=php&logoColor=white"></a>
  <a href="https://laravel.com"><img alt="Laravel" src="https://img.shields.io/badge/Laravel-13-FF2D20?style=flat-square&logo=laravel&logoColor=white"></a>
</p>

<p align="center">
  <a href="#-the-problem">The problem</a> ·
  <a href="#-what-qualyra-does">What it does</a> ·
  <a href="#-try-it-in-1-minute">Try it</a> ·
  <a href="#-the-story">The story</a> ·
  <a href="#-contributing">Contribute</a>
</p>

<p align="center">
  <img src="docs/screenshots/dashboard.png" alt="Qualyra dashboard: 30 AI use cases classified by risk level" width="900">
</p>

> [!WARNING]
> **Qualyra is a diagnostic aid, not legal advice.**
> Classification relies on what the user declares and on a reading of Regulation (EU) 2024/1689 and the GDPR at a given date. It may be incomplete or out of date. Have any compliance decision validated by a lawyer or a DPO. Provided "as is", without warranty ([LICENSE](LICENSE)).

> [!NOTE]
> The application UI and generated reports are in **French** (it was built for the French market). The rules engine, the code and this documentation are language-neutral and easy to adapt.

---

## 🎯 The problem

A 50-person company today uses ChatGPT for writing, a tool that screens CVs, a chatbot on its website, maybe customer scoring. **Nobody knows which of these fall under the EU AI Act**, or what to do about it.

| | |
|---|---|
| ⚖️ **€35M or 7% of global turnover** | Maximum fine for a prohibited AI practice |
| 📅 **Already in force** | Prohibited practices since Feb 2, 2025; transparency duties (Art. 50) since Aug 2, 2026 |
| ⏳ **December 2, 2027** | High-risk obligations kick in (hiring, credit, education…) |
| 💸 **€10k–50k** | Typical cost of a consulting engagement, out of reach for most SMEs |

## ✨ What Qualyra does

```
  Declare AI use cases  →  Answer a questionnaire  →  Automatic classification  →  PDF report + action plan
  (ChatGPT, CV screening…)   (adapts to each use)       (4 AI Act risk levels)       (1 month · 6 months · 1 year)
```

- 🧠 **A 22-rule engine** that turns the regulation into logic: 8 prohibited practices, 8 high-risk cases, 6 transparency obligations.
- 🗓️ **Time-aware rules**: each rule has its own entry-into-force date, and the tool shows what will flip in 1 and 2 years. Calendar updated for the **Digital Omnibus** (July 2026).
- 🔐 **GDPR side by side**: legal basis, transfers outside the EU, processors, plus AI vendor tracking (OpenAI, Anthropic, Mistral…).
- 📄 **A report written for executives**: summary, per-use-case detail, action plan, checklist, grey areas. Content is frozen at generation time for traceability.
- 🏢 **Multi-tenant** with strict data isolation between organizations.

### Screenshots

| Risk map | Compliance report |
|---|---|
| ![Domain → AI type → risk level flow](docs/screenshots/vision.png) | ![Audit report](docs/screenshots/report.png) |

<p align="center">
  <img src="docs/screenshots/landing.png" alt="Qualyra landing page" width="900">
</p>

### The 4 risk levels

| Level | Examples | Applies from | Max fine |
|---|---|---|---|
| 🔴 **Unacceptable** | Social scoring, emotion recognition at work, manipulation | 2025-02-02 | €35M or 7% |
| 🟠 **High risk** (Annex III) | CV screening, credit scoring, education, biometrics | 2027-12-02 | €15M or 3% |
| 🟠 **High risk** (Annex I) | AI embedded in a medical device or machinery | 2028-08-02 | €15M or 3% |
| 🟡 **Limited risk** (Art. 50) | Chatbots, deepfakes, generated content | 2026-08-02 | €15M or 3% |
| 🟢 **Minimal risk** | Everything else | No obligation | — |

High-risk dates reflect the **Digital Omnibus on AI** (in force since 2026-07-27), which postponed the original deadlines of 2026-08-02 and 2027-08-02.

## 🚀 Try it in 1 minute

All you need is Docker:

```bash
git clone https://github.com/tomtimlt/qualyra.git && cd qualyra
docker build -t qualyra . && docker run -d -p 8000:8000 qualyra
```

Open **http://localhost:8000** and log in with **`demo@example.com`** / **`password`**. The demo account contains a fictional company (Nova Conseil & Services) with 30 AI use cases already declared and reports generated.

<details>
<summary><strong>Install without Docker</strong></summary>

Requirements: PHP 8.4, Composer 2, Node 20+.

```bash
composer install
npm install && npm run build
cp .env.example .env && php artisan key:generate
touch database/database.sqlite
php artisan migrate --seed
composer dev   # server + queue + logs + Vite
```

PDF rendering uses headless Chrome. If Chromium is not at `/usr/bin/chromium`, set `CHROME_PATH` in `.env`.

Stripe payment is optional: without `STRIPE_SECRET`, reports are generated for free.

</details>

## 📖 The story

Qualyra started from a simple observation: the AI Act is coming, and French SMEs have neither in-house legal expertise nor the budget for a consulting firm. I wanted to give them a concrete answer: a report for around €1,500 instead of tens of thousands.

I designed and built the product on my own: reading the regulation, turning it into testable rules, the web app, the PDF report, payments. The commercial project didn't take off. **Rather than let it sit in a drawer, I'm releasing it as open source** so the rules engine and the approach can be useful to others: developers, DPOs, GRC consultants, students.

If you work on AI compliance, I'd love your feedback, especially where a rule is wrong.

## 🛠️ Under the hood

| | |
|---|---|
| **Backend** | PHP 8.4 · Laravel 13 · SQLite / MySQL |
| **Frontend** | Blade · Alpine.js · Tailwind CSS 4 · Vite |
| **PDF** | Browsershot (headless Chrome) |
| **Payments** | Stripe Checkout (optional) |
| **Quality** | 136 Pest tests · Pint · GitHub Actions CI · Dependabot |
| **Deployment** | Docker, self-contained image |

The core of the project fits in a few files:

- [`config/ai_act_rules.php`](config/ai_act_rules.php): the 22 rules, each with its condition, legal reference and date of application.
- [`app/Services/AiActClassifier.php`](app/Services/AiActClassifier.php): the engine. Rules are evaluated in order of severity (`UNACCEPTABLE > HIGH_RISK > LIMITED_RISK > DEFAULT`) and the first match wins, with GDPR alerts layered on top.
- [`app/Services/ComplianceTimelineBuilder.php`](app/Services/ComplianceTimelineBuilder.php): the 1- and 2-year projection.

<details>
<summary><strong>Technical details</strong></summary>

### Architecture

```
app/
├── Http/Controllers/   # AI use cases, questionnaire, assessment, report, checkout
├── Services/           # AiActClassifier, ReportContentBuilder, ReportSnapshotBuilder, timeline
├── Models/             # User, Organization, AiUsage, Response, Assessment, Report
└── Policies/           # AiUsagePolicy (tenant isolation)

config/
├── ai_act_rules.php       # the 22 AI Act rules
├── questionnaire.php      # dynamic questions by AI type / domain
└── report_templates.php   # report copy (French)
```

More in [`docs/ARCHITECTURE.md`](docs/ARCHITECTURE.md) and [`docs/DB_SCHEMA.md`](docs/DB_SCHEMA.md) (French).

### Security

- **Strict tenant isolation**: every resource is scoped to the user's organization, with cross-tenant access (IDOR) tests.
- **Frozen snapshot**: each report stores an immutable JSON of the assessment at generation time.
- **No mass assignment**: foreign keys are set through Eloquent relations.
- **Centralized validation** with typed Form Requests; native Laravel CSRF.

Report vulnerabilities privately: see [`SECURITY.md`](SECURITY.md).

### Tests

```bash
php artisan test                   # full suite
php artisan test --filter Report   # filtered
```

PDF download tests launch a real headless Chrome: `CHROME_PATH=/path/to/chrome php artisan test`.

</details>

## 🤝 Contributing

Contributions are welcome, especially on regulatory accuracy: a mis-encoded rule, an outdated date or a poorly described grey area is a bug in its own right.

- Bug or idea: open an [issue](https://github.com/tomtimlt/qualyra/issues).
- Fix: open a pull request directly (see [CONTRIBUTING.md](CONTRIBUTING.md)). English or French, both are fine.
- Security issue: follow [SECURITY.md](SECURITY.md).

If the project is useful to you, a ⭐ helps spread the word.

## 📜 License

© 2026 Thomas Lhostete, released under the **[GNU AGPL-3.0](LICENSE)**.

You are free to use, modify and redistribute Qualyra. If you offer it as an online service, you must publish the source code of your version under the same license. Generated reports are not legal advice.

---

<p align="center">
  Built for European SMEs by <a href="https://github.com/tomtimlt">@tomtimlt</a> · CESI Nancy<br>
  <sub>Contact: thomas.lhostete@viacesi.fr</sub>
</p>
