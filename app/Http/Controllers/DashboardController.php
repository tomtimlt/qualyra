<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Affiche le tableau de bord.
     *
     * Si l'utilisateur n'a pas encore créé son organisation, on présente
     * un appel à l'action pour la créer. Sinon on affiche la liste de ses
     * usages d'IA déclarés.
     */
    public function __invoke(Request $request): View
    {
        $organization = $request->user()->organization;

        $aiUsages = $organization
            ? $organization->aiUsages()->latest()->get()
            : collect();

        return view('dashboard', [
            'organization' => $organization,
            'aiUsages' => $aiUsages,
        ]);
    }
}
