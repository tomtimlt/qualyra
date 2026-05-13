## Description

Expliquez clairement ce que cette PR apporte.

## Type de changement

- [ ] Bug fix
- [ ] Feature
- [ ] Refactor
- [ ] Doc
- [ ] Config (HOT SPOT)

## Hot spots touchés

- [ ] config/ai_act_rules.php
- [ ] config/questionnaire.php
- [ ] config/report_templates.php
- [ ] app/Services/AiActClassifier.php
- [ ] app/Services/ReportContentBuilder.php
- [ ] app/Services/ReportSnapshotBuilder.php
- [ ] app/Policies/AiUsagePolicy.php
- [ ] database/seeders/DemoSeeder.php
- [ ] resources/views/reports/pdf.blade.php

## Checklist

- [ ] `./scripts/check-sync.sh` passe (config ↔ seeder synchronisé)
- [ ] `php artisan test` passe (102+ tests)
- [ ] `./vendor/bin/pint --test` passe (lint)
- [ ] `npm run build` passe (assets)
- [ ] Documentation mise à jour si convention change
- [ ] AGENTS.md scoped mis à jour si structure change

## IA assistance

- [ ] Cette PR a été assistée par une IA
- [ ] L'IA a lu AGENTS.md
- [ ] L'IA a respecté l'impact minimal
