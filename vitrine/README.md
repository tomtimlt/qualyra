# Qualyra — Site vitrine (mini-app Laravel autonome)

## Déploiement rapide (VPS)

```bash
cd vitrine
docker compose up -d --build
```

- **Vitrine** : http://localhost:8080
- **Umami** (analytics) : http://localhost:3000

Mettre derrière un reverse proxy (Caddy, Nginx, Traefik) avec TLS pour le domaine public.

## Configuration

Copier `.env.example` vers `.env` (ou le faire automatiquement au démarrage) :

```bash
cp .env.example .env
```

Variables essentielles :

| Variable | Description |
|---|---|
| `VITRINE_ADMIN_PASSWORD` | Mot de passe du panel `/admin` |
| `VITRINE_CONTACT_EMAIL` | Email de contact affiché dans les boutons |
| `VITRINE_UMAMI_SRC` | URL publique du script Umami (ex: `https://analytics.example.com/script.js`) |
| `VITRINE_UMAMI_WEBSITE_ID` | ID du site créé dans l'interface Umami |
| `VITRINE_UMAMI_DASHBOARD` | URL du dashboard Umami (ex: `https://analytics.example.com`) |

## Première configuration Umami

1. Démarrer les services : `docker compose up -d --build`
2. Ouvrir http://localhost:3000
3. Se connecter avec les identifiants par défaut : `admin` / `umami`
4. Changer le mot de passe immédiatement
5. Créer un site → obtenir le `website-id`
6. Renseigner `VITRINE_UMAMI_SRC`, `VITRINE_UMAMI_WEBSITE_ID` et `VITRINE_UMAMI_DASHBOARD` dans le `.env` de la vitrine
7. Redéployer : `docker compose up -d --build`

## Maintenance des aperçus

Les 5 PNG d'aperçu (`vitrine/public/qualyra/img/`) sont produits par le script
`scripts/generate-landing-previews.js` depuis l'application Laravel principale.

Quand l'UI du dashboard ou du rapport change :

1. Régénérez les aperçus dans l'app : `node scripts/generate-landing-previews.js`
2. Recopiez les PNG vers la vitrine :
   ```bash
   cp public/qualyra/img/preview-*.png vitrine/public/qualyra/img/
   ```
3. Rebuild : `docker compose up -d --build`

## Structure

```
vitrine/
├── Dockerfile
├── docker-compose.yml           (vitrine + umami + umami-db)
├── docker/entrypoint.sh
├── .env.example
├── README.md
├── app/
│   ├── Http/Controllers/        (Home, Contact, Admin, Locale)
│   ├── Http/Middleware/          (SetLocale, EnsureAdmin)
│   └── Models/ContactMessage.php
├── config/vitrine.php
├── database/migrations/
├── lang/                        (fr.json + en.json)
├── resources/views/
│   ├── layouts/                 (public, admin)
│   ├── home.blade.php
│   ├── contact.blade.php
│   └── admin/                   (login, dashboard)
└── public/
    ├── js/                      (brain.js, site.js)
    ├── qualyra/css/qualyra.css
    ├── qualyra/img/             (5 PNG d'aperçu)
    ├── qualyra/brand/
    └── fonts/                   (Geist, Geist Mono, Instrument Serif)
```
