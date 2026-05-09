<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrganizationRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrganizationController extends Controller
{
    /**
     * Formulaire de création de l'organisation.
     *
     * Si l'utilisateur a déjà une organisation, on le redirige vers
     * son tableau de bord (1 user = 1 PME, pas de duplication possible).
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
}
