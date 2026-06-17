<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrganizationRequest;
use App\Http\Requests\UpdateOrganizationRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrganizationController extends Controller
{
    /**
     * Formulaire de création de l'organisation.
     *
     * Si l'utilisateur a déjà une organisation, on le redirige vers
     * son tableau de bord (1 user = 1 organisation, pas de duplication possible).
     */
    public function create(Request $request): View|RedirectResponse
    {
        if ($request->user()->organization !== null) {
            return redirect()->route('dashboard');
        }

        return view('organization.create');
    }

    /**
     * Persiste la nouvelle organisation rattachée à l'utilisateur courant.
     *
     * L'autorisation (utilisateur sans organisation existante) est gérée
     * par StoreOrganizationRequest::authorize().
     */
    public function store(StoreOrganizationRequest $request): RedirectResponse
    {
        $request->user()->organization()->create($request->validated());

        return redirect()
            ->route('dashboard')
            ->with('status', 'organization-created');
    }

    /**
     * Affiche la fiche de l'organisation. Sans organisation, on redirige
     * l'utilisateur vers la création.
     */
    public function show(Request $request): View|RedirectResponse
    {
        $organization = $request->user()->organization;

        if ($organization === null) {
            return redirect()->route('organization.create');
        }

        return view('organization.show', ['organization' => $organization]);
    }

    /**
     * Formulaire d'édition de l'organisation rattachée au compte courant.
     */
    public function edit(Request $request): View|RedirectResponse
    {
        $organization = $request->user()->organization;

        if ($organization === null) {
            return redirect()->route('organization.create');
        }

        return view('organization.edit', ['organization' => $organization]);
    }

    /**
     * Persiste les modifications de l'organisation. La FormRequest garantit
     * que la cible est toujours l'organisation de l'utilisateur courant
     * (pas d'ID dans l'URL, donc pas d'IDOR possible).
     */
    public function update(UpdateOrganizationRequest $request): RedirectResponse
    {
        $organization = $request->user()->organization;
        $organization->update($request->validated());

        return redirect()
            ->route('organization.show')
            ->with('status', 'organization-updated');
    }
}
