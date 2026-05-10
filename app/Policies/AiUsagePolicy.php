<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\AiUsage;
use App\Models\User;

class AiUsagePolicy
{
    /**
     * Tout utilisateur authentifié peut lister ses propres usages.
     * Le filtrage par organisation est appliqué côté contrôleur.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * L'utilisateur ne peut consulter qu'un usage appartenant à son
     * organisation. Sans organisation, aucun usage n'est consultable.
     */
    public function view(User $user, AiUsage $aiUsage): bool
    {
        return $this->ownsUsage($user, $aiUsage);
    }

    /**
     * SÉCURITÉ : la création d'un usage requiert une organisation, mais
     * cette règle est appliquée en amont (deux barrages indépendants) :
     *   1. POST /usages : refus 403 via StoreAiUsageRequest::authorize()
     *   2. GET /usages/create : redirection UX dans AiUsageController::create()
     *
     * On ne met PAS le check ici, sinon authorizeResource (middleware
     * `can:create,AiUsage`) renverrait un 403 brutal avant que le
     * contrôleur ait pu rediriger vers organization.create.
     *
     * Tests couvrant les deux barrages : tests/Feature/AiUsageTest.php
     * (« refuse la création d'usage si le user n'a pas encore d'organisation »).
     */
    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, AiUsage $aiUsage): bool
    {
        return $this->ownsUsage($user, $aiUsage);
    }

    public function delete(User $user, AiUsage $aiUsage): bool
    {
        return $this->ownsUsage($user, $aiUsage);
    }

    /**
     * Vérifie qu'un usage appartient à l'organisation de l'utilisateur.
     * Cette unique source de vérité évite la duplication de logique
     * d'autorisation entre view/update/delete.
     */
    private function ownsUsage(User $user, AiUsage $aiUsage): bool
    {
        $organization = $user->organization;

        if ($organization === null) {
            return false;
        }

        return $aiUsage->organization_id === $organization->id;
    }
}
