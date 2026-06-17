<?php

declare(strict_types=1);

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'theme_preference'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Cache par requête : organization_id => role (évite N requêtes pivot).
     *
     * @var array<int, string>|null
     */
    private ?array $membershipRolesCache = null;

    /** Cache par requête de l'organisation courante résolue. */
    private ?Organization $currentOrganizationCache = null;

    private bool $currentOrganizationResolved = false;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Organisation rattachée au compte utilisateur.
     * Relation 1-1 : un user = une organisation.
     *
     * @deprecated Héritage du modèle 1 user = 1 organisation — remplacée par
     *             organizations() / currentOrganization(). Supprimée en PR3.
     */
    public function organization(): HasOne
    {
        return $this->hasOne(Organization::class);
    }

    /**
     * Toutes les organisations auxquelles le user a accès (memberships),
     * avec son rôle dans chacune (owner / member / auditor).
     */
    public function organizations(): BelongsToMany
    {
        return $this->belongsToMany(Organization::class)
            ->withPivot('role')
            ->withTimestamps();
    }

    /**
     * Rôle du user dans une organisation donnée, ou null s'il n'en est pas
     * membre. SOURCE UNIQUE DE VÉRITÉ pour l'autorisation (Policies) :
     * ne dépend jamais de la session, uniquement du pivot en base.
     */
    public function membershipRole(int $organizationId): ?string
    {
        if ($this->membershipRolesCache === null) {
            $this->membershipRolesCache = $this->organizations()
                ->pluck('organization_user.role', 'organizations.id')
                ->all();
        }

        return $this->membershipRolesCache[$organizationId] ?? null;
    }

    /**
     * Le user a-t-il au moins une mission d'audit (membership auditor) ?
     */
    public function isAuditor(): bool
    {
        if ($this->membershipRolesCache === null) {
            $this->membershipRolesCache = $this->organizations()
                ->pluck('organization_user.role', 'organizations.id')
                ->all();
        }

        return in_array('auditor', $this->membershipRolesCache, true);
    }

    /**
     * Organisation « courante » du user (contexte UI, switchable en session).
     *
     * Résolution :
     *   1. session('current_organization_id') si elle correspond à une vraie
     *      membership (anti-tampering : un id forgé en session est ignoré) ;
     *   2. sinon fallback sur la première membership (owner prioritaire).
     *
     * Ce contexte ne sert QUE pour l'affichage et le scoping des listes —
     * jamais pour l'autorisation (voir membershipRole()).
     */
    public function currentOrganization(): ?Organization
    {
        if ($this->currentOrganizationResolved) {
            return $this->currentOrganizationCache;
        }

        $this->currentOrganizationResolved = true;

        $sessionId = session('current_organization_id');

        if ($sessionId !== null) {
            $organization = $this->organizations()->find($sessionId);

            if ($organization !== null) {
                return $this->currentOrganizationCache = $organization;
            }

            session()->forget('current_organization_id');
        }

        $organization = $this->organizations()
            ->orderByRaw("CASE WHEN organization_user.role = 'owner' THEN 0 ELSE 1 END")
            ->orderBy('organization_user.created_at')
            ->first();

        if ($organization !== null) {
            session(['current_organization_id' => $organization->id]);
        }

        return $this->currentOrganizationCache = $organization;
    }
}
