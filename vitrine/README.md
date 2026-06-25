# Qualyra — Site vitrine (mini-app Laravel autonome)

## Déploiement (VPS)

```bash
cd vitrine
cp .env.example .env          # 1. créer le fichier d'environnement
nano .env                     # 2. remplir les secrets (voir ci-dessous)
docker compose up -d --build  # 3. lancer
```

> Le `docker compose up` **échoue volontairement** tant que `VITRINE_ADMIN_PASSWORD` et
> `UMAMI_APP_SECRET` ne sont pas définis dans `.env` — c'est voulu, pour ne jamais démarrer
> avec des secrets vides ou publics.

- **Vitrine** : http://localhost:8080
- **Umami** (analytics) : http://localhost:3000

Mettre derrière un reverse proxy (Caddy, Nginx, Traefik) avec TLS pour le domaine public.

## Configuration (`vitrine/.env`)

Le `.env` (non versionné) alimente **à la fois** Laravel et l'interpolation `${...}` du
`docker-compose.yml`.

| Variable | Description |
|---|---|
| `APP_URL` | **En prod : `https://ton-domaine.fr`** (sinon les redirections admin pointent vers localhost) |
| `VITRINE_ADMIN_PASSWORD` | **Requis.** Mot de passe du panel `/admin` — `openssl rand -base64 24` |
| `UMAMI_APP_SECRET` | **Requis.** Secret de session Umami — `openssl rand -base64 40` |
| `UMAMI_DB_PASSWORD` | Mot de passe Postgres d'Umami (réseau interne, non exposé) |
| `VITRINE_CONTACT_EMAIL` | Email de contact affiché dans les boutons |
| `VITRINE_UMAMI_SRC` | URL publique du script Umami (ex: `https://analytics.example.com/script.js`) |
| `VITRINE_UMAMI_WEBSITE_ID` | ID du site créé dans l'interface Umami |
| `VITRINE_UMAMI_DASHBOARD` | URL du dashboard Umami (ex: `https://analytics.example.com`) |

### Durcissement prod (important)

- ⚠️ L'ancien mot de passe admin codé en dur (`G00f1@d3m0n`) reste dans l'historique git :
  **choisis-en un nouveau**, ne le réutilise pas.
- `APP_ENV=production` / `APP_DEBUG=false` sont les valeurs par défaut du `.env.example`.
- Le port Umami `3000` est public : restreins-le (firewall) ou expose-le seulement derrière le
  reverse proxy + TLS. Change aussi le login Umami par défaut `admin`/`umami` dès la 1re connexion.

## Première configuration Umami

1. Démarrer les services : `docker compose up -d --build`
2. Ouvrir http://localhost:3000
3. Se connecter avec les identifiants par défaut : `admin` / `umami`
4. Changer le mot de passe immédiatement
5. Créer un site → obtenir le `website-id`
6. Renseigner `VITRINE_UMAMI_SRC`, `VITRINE_UMAMI_WEBSITE_ID` et `VITRINE_UMAMI_DASHBOARD` dans le `.env` de la vitrine
7. Recharger (pas besoin de rebuild) : `docker compose up -d` — recrée le conteneur vitrine et recache la config

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
