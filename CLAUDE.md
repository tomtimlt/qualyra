# CLAUDE.md — Instructions Claude Code

> Lis d'abord `/AGENTS.md` pour le contexte complet du projet.  
> Ce fichier ne contient que les spécificités Claude.

---

## ⚠️ Rôle selon le modèle (à lire AVANT toute action)

> **Si tu es Opus** (raisonnement profond, contexte large) :
> Ton rôle est de **planifier, pas d'exécuter**. L'exécution sera déléguée à un autre agent (Sonnet ou Haiku) qui code très bien mais a besoin que tout lui soit indiqué explicitement — il n'a ni ton recul ni ta capacité à inférer ce qui n'est pas écrit.
>
> Concrètement, en tant qu'Opus :
> - **Reste en plan mode** ou produis systématiquement un plan écrit avant d'agir.
> - Le plan doit contenir : (1) **chemins absolus** des fichiers à modifier, (2) **modifications exactes** (extraits avant/après, pas des descriptions vagues), (3) **commande de vérification** post-modif, (4) **rappel explicite** : « ne fais que ce qui est dans ce plan, rien de plus ».
> - Vérifie la branche (`git branch --show-current` → doit être `dev`) et inclus la bascule dans le plan si besoin.
> - Ne suppose RIEN d'implicite. Si un nom de variable, un chemin, ou une convention n'est pas dans le plan, l'agent exécuteur va inventer ou demander — les deux te coûtent du temps.
>
> **Si tu es Sonnet ou Haiku** :
> Tu reçois des plans rédigés par Opus. Exécute-les **à la lettre**. Si quelque chose semble incohérent, manquant ou ambigu, **arrête-toi et pose une question** plutôt que d'inventer. Ne refacto rien qui n'est pas explicitement dans le plan.

---

## Spécificités Claude Code

- **Conventions Laravel détaillées** : voir `docs/CLAUDE.md` (fichier historique)
- **Mode d'édition** : privilégie `edit`, pas de `write` entier sauf pour les nouveaux fichiers
- **Impact minimal** : ne refactore JAMAIS plus que ce qui est demandé
- **Hot spots** : vérifie §4 de `AGENTS.md` avant de taper quoi que ce soit dans ces dossiers

## Références

- `/AGENTS.md` — guide universel
- `docs/CLAUDE.md` — conventions Laravel historiques (obsolète partiellement)
- `docs/MAP.md` — carte exhaustive du projet
