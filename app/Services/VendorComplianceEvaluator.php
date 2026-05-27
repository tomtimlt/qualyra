<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\AiVendor;

class VendorComplianceEvaluator
{
    /**
     * Évalue le niveau de conformité contractuelle d'un fournisseur IA.
     *
     * Retourne un score `complet` / `partiel` / `manquant` selon les
     * trois critères contractuels :
     *   - Déclaration de conformité Art. 47 AI Act
     *   - DPA Art. 28 RGPD signé
     *   - CCT (Décision UE 2021/914) — n'est requis que pour les vendors
     *     hors UE ; pour les vendors UE, il vaut "N/A" et ne pénalise pas.
     *
     * Sert au rapport pour produire l'encadré "Chaîne d'approvisionnement"
     * par usage et à la liste vendors pour signaler les non-conformités.
     *
     * @return array<string, mixed>
     */
    public function evaluate(AiVendor $vendor): array
    {
        $criteria = [
            'art47' => (bool) $vendor->declaration_conformite_art47,
            'dpa' => (bool) $vendor->dpa_art28_signe,
        ];

        // CCT seulement pertinent si fournisseur hors UE.
        $cctRequired = (bool) $vendor->hors_ue;
        $cctOk = $cctRequired ? ((bool) $vendor->cct_signees) : true;
        $criteria['cct'] = $cctOk;

        $okCount = count(array_filter($criteria));
        $totalCount = count($criteria);

        $status = match (true) {
            $okCount === $totalCount => 'complet',
            $okCount === 0 => 'manquant',
            default => 'partiel',
        };

        return [
            'status' => $status,
            'criteria' => $criteria,
            'cct_required' => $cctRequired,
            'gaps' => $this->describeGaps($criteria, $cctRequired),
        ];
    }

    /**
     * @param  array<string, bool>  $criteria
     * @return array<int, string>
     */
    private function describeGaps(array $criteria, bool $cctRequired): array
    {
        $gaps = [];

        if (! $criteria['art47']) {
            $gaps[] = 'Déclaration de conformité Art. 47 AI Act non reçue.';
        }
        if (! $criteria['dpa']) {
            $gaps[] = 'Contrat de sous-traitance Art. 28 RGPD non signé.';
        }
        if ($cctRequired && ! $criteria['cct']) {
            $gaps[] = 'Clauses contractuelles types non signées (fournisseur hors UE).';
        }

        return $gaps;
    }
}
