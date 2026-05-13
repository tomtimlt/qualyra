# AGENTS.md — resources/views/reports/

## Rôle

Template PDF du rapport de conformité. Généré par Chrome headless via `spatie/browsershot`. **Contraintes spécifiques** : le rendu PDF n'utilise pas un navigateur standard, certaines fonctionnalités CSS modernes ne fonctionnent pas.

## Fichiers principaux

| Fichier | Rôle | Hot spot |
|---------|------|----------|
| `pdf.blade.php` | Template PDF standalone (Chrome headless) | ✅ |
| `show.blade.php` | Vue web du rapport (affichée dans l'application) | ❌ |
| `index.blade.php` | Liste des rapports | ❌ |

## Règles de modification de `pdf.blade.php`

- **Pas de CSS moderne** : éviter `display: grid`, `gap`, `backdrop-filter`, `aspect-ratio`, `var()` — ils peuvent ne pas être supportés par le mode headless de Chrome
- **Polices** : utiliser les polices système uniquement (Geist, Geist Mono) ou polices du système
- **Images** : en base64 (pas de lien relatif — le PDF est autonome)
- **Mise en page** : préférer `display: table`, `inline-block`, `float` pour les layouts complexes
- **Sauts de page** : utiliser `page-break-before`, `page-break-after`, `page-break-inside`
- **Impression** : les styles d'impression sont intégrés dans le fichier (pas de `@media print` externe)

## Tests associés

- `tests/Feature/ReportTest.php` — valide la génération du PDF
- Vérification manuelle : le PDF est stocké dans `storage/app/private/reports/`

## Voir aussi

- `/AGENTS.md` (racine)
- `config/AGENTS.md` — les templates de contenu utilisés par le PDF
- `app/Services/ReportContentBuilder.php` — assemble le contenu du PDF
- `app/Services/ReportSnapshotBuilder.php` — données figées du rapport
