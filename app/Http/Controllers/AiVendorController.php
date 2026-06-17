<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreAiVendorRequest;
use App\Http\Requests\UpdateAiVendorRequest;
use App\Models\AiVendor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\View\View;

class AiVendorController extends Controller implements HasMiddleware
{
    /**
     * Câble chaque action sur AiVendorPolicy.
     */
    public static function middleware(): array
    {
        return [
            new Middleware('can:viewAny,'.AiVendor::class, only: ['index']),
            new Middleware('can:view,aiVendor', only: ['show']),
            new Middleware('can:create,'.AiVendor::class, only: ['create', 'store']),
            new Middleware('can:update,aiVendor', only: ['edit', 'update']),
            new Middleware('can:delete,aiVendor', only: ['destroy']),
        ];
    }

    public function index(Request $request): View|RedirectResponse
    {
        $organization = $request->user()->organization;

        if ($organization === null) {
            return redirect()->route('organization.create');
        }

        $vendors = $organization->aiVendors()->withCount('aiUsages')->latest()->get();

        return view('vendors.index', compact('vendors'));
    }

    public function create(Request $request): View|RedirectResponse
    {
        if ($request->user()->organization === null) {
            return redirect()->route('organization.create');
        }

        return view('vendors.create');
    }

    /**
     * organization_id forcé côté serveur — jamais lu depuis le request.
     */
    public function store(StoreAiVendorRequest $request): RedirectResponse
    {
        $organization = $request->user()->organization;
        abort_if($organization === null, 403);

        $organization->aiVendors()->create($request->validated());

        return redirect()
            ->route('vendors.index')
            ->with('status', 'vendor-created');
    }

    public function show(AiVendor $aiVendor): View
    {
        $aiVendor->loadCount('aiUsages');

        return view('vendors.show', ['vendor' => $aiVendor]);
    }

    public function edit(AiVendor $aiVendor): View
    {
        return view('vendors.edit', ['vendor' => $aiVendor]);
    }

    public function update(UpdateAiVendorRequest $request, AiVendor $aiVendor): RedirectResponse
    {
        $aiVendor->update($request->validated());

        return redirect()
            ->route('vendors.index')
            ->with('status', 'vendor-updated');
    }

    public function destroy(AiVendor $aiVendor): RedirectResponse
    {
        $aiVendor->delete();

        return redirect()
            ->route('vendors.index')
            ->with('status', 'vendor-deleted');
    }
}
