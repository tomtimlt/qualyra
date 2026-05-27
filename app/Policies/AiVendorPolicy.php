<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\AiVendor;
use App\Models\User;

class AiVendorPolicy
{
    /**
     * Tout utilisateur authentifié peut lister ses propres vendors.
     * Le filtrage par organisation est appliqué côté contrôleur.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, AiVendor $vendor): bool
    {
        return $this->ownsVendor($user, $vendor);
    }

    /**
     * Comme pour AiUsage : on autorise au niveau policy, la vérification
     * "a une organisation" est faite dans le controller pour pouvoir rediriger
     * vers organization.create plutôt que de renvoyer un 403 brut.
     */
    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, AiVendor $vendor): bool
    {
        return $this->ownsVendor($user, $vendor);
    }

    public function delete(User $user, AiVendor $vendor): bool
    {
        return $this->ownsVendor($user, $vendor);
    }

    /**
     * Vérifie qu'un vendor appartient à l'organisation de l'utilisateur.
     * Source unique de vérité pour view/update/delete.
     */
    private function ownsVendor(User $user, AiVendor $vendor): bool
    {
        $organization = $user->organization;

        if ($organization === null) {
            return false;
        }

        return $vendor->organization_id === $organization->id;
    }
}
