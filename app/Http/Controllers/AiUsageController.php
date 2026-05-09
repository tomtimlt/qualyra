<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreAiUsageRequest;
use App\Http\Requests\UpdateAiUsageRequest;
use App\Models\AiUsage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\View\View;

class AiUsageController extends Controller implements HasMiddleware
{
    /**
     * Câble chaque action du contrôleur sur AiUsagePolicy.
     * Équivalent de l'ancien authorizeResource() (Laravel <= 10).
     */
    public static function middleware(): array
    {
        return [
            new Middleware('can:viewAny,'.AiUsage::class, only: ['index']),
            new Middleware('can:view,aiUsage', only: ['show']),
            new Middleware('can:create,'.AiUsage::class, only: ['create', 'store']),
            new Middleware('can:update,aiUsage', only: ['edit', 'update']),
            new Middleware('can:delete,aiUsage', only: ['destroy']),
        ];
    }

    /**
     * Liste les usages IA déclarés par l'organisation de l'utilisateur.
     */
    public function index(Request $request): View|RedirectResponse
    {
        $organization = $request->user()->organization;

        // Sans organisation, on redirige vers le formulaire de création
        if ($organization === null) {
            return redirect()->route('organization.create');
        }

        $aiUsages = $organization->aiUsages()->latest()->get();

        return view('usages.index', compact('aiUsages'));
    }

    public function create(Request $request): View|RedirectResponse
    {
        if ($request->user()->organization === null) {
            return redirect()->route('organization.create');
        }

        return view('usages.create');
    }

    /**
     * Persiste un nouvel usage IA rattaché à l'organisation de l'utilisateur.
     * L'organization_id est forcé côté serveur — jamais lu depuis le request.
     */
    public function store(StoreAiUsageRequest $request): RedirectResponse
    {
        $organization = $request->user()->organization;

        // Garde redondante : StoreAiUsageRequest::authorize() bloque déjà
        // ce cas, mais on protège contre un futur appel direct du controller
        // (ex : test, console, refacto FormRequest) qui causerait un null deref.
        abort_if($organization === null, 403);

        $organization->aiUsages()->create($request->validated());

        return redirect()
            ->route('usages.index')
            ->with('status', 'usage-created');
    }

    public function show(AiUsage $aiUsage): View
    {
        return view('usages.show', ['aiUsage' => $aiUsage]);
    }

    public function edit(AiUsage $aiUsage): View
    {
        return view('usages.edit', ['aiUsage' => $aiUsage]);
    }

    public function update(UpdateAiUsageRequest $request, AiUsage $aiUsage): RedirectResponse
    {
        $aiUsage->update($request->validated());

        return redirect()
            ->route('usages.index')
            ->with('status', 'usage-updated');
    }

    public function destroy(AiUsage $aiUsage): RedirectResponse
    {
        $aiUsage->delete();

        return redirect()
            ->route('usages.index')
            ->with('status', 'usage-deleted');
    }
}
