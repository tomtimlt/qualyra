# Schéma de base de données — v1

## Vue d'ensemble

5 tables principales :
- users (Laravel Breeze)
- organizations (la PME cliente)
- ai_usages (chaque outil IA déclaré par le client)
- responses (les réponses au questionnaire pour chaque usage)
- assessments (le résultat de la classification + données du rapport)

## users (table standard Breeze)
- id, name, email, password, email_verified_at, remember_token, timestamps

## organizations
- id (pk)
- user_id (fk users.id) — propriétaire du compte
- name (string) — nom de la PME
- siret (string, nullable)
- size (enum: '1-19', '20-49', '50-149', '150+')
- sector (string, nullable) — secteur d'activité libre
- created_at, updated_at

## ai_usages
- id (pk)
- organization_id (fk)
- name (string) — nom donné par le client (ex : "ChatGPT pour les CV")
- description (text, nullable)
- type (enum: LLM_GEN, IA_GEN, IA_SCORING, IA_BIO, AUTRE)
- domain (enum: RH, EDUCATION, CREDIT, SANTE, SECURITE, MARKETING, 
  PROD_INT, DEV_LOG, AUTRE)
- created_at, updated_at

## responses
- id (pk)
- ai_usage_id (fk)
- variable_key (string) — ex: "DEC", "PUB", "DATA", "RH_USAGE"
- variable_value (string) — ex: "INFORMATIF", "EMPLOYES,VULNERABLES"
- created_at, updated_at

## assessments
- id (pk)
- ai_usage_id (fk)
- niveau (enum: INACCEPTABLE, HAUT_RISQUE, RISQUE_LIMITE, RISQUE_MINIMAL)
- regle_id (string) — ex: "R-H-02", "R-I-01", "DEFAULT"
- article (string)
- raison (text)
- alertes (json) — tableau des alertes type FLAG_ZONE_GRISE, AGGRAVATION
- type_regle (enum: TEXTE_EXPLICITE, INTERPRETATION, NA)
- computed_at (timestamp)
- created_at, updated_at

## payments (à ajouter en semaine 5)
- id (pk)
- organization_id (fk)
- stripe_session_id (string)
- amount (integer, en centimes)
- status (enum: pending, paid, failed)
- paid_at (timestamp, nullable)
- created_at, updated_at

## reports (à ajouter en semaine 5)
- id (pk)
- organization_id (fk)
- pdf_path (string) — chemin vers le PDF généré
- generated_at (timestamp)
- created_at, updated_at
