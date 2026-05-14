<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Report;
use App\Services\ReportSnapshotBuilder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe\StripeClient;

class CheckoutController extends Controller
{
    /**
     * Crée un Report en attente puis redirige vers Stripe Checkout.
     *
     * Choix d'archi :
     *   - On capture le snapshot AVANT le paiement, pour que ce qui est
     *     payé corresponde exactement à ce que l'utilisateur a vu/validé.
     *   - paid_at reste null jusqu'au retour success → l'historique distingue
     *     les rapports payés des sessions abandonnées.
     */
    public function create(Request $request, ReportSnapshotBuilder $builder): RedirectResponse
    {
        $organization = $request->user()->organization;
        if ($organization === null) {
            return redirect()->route('organization.create');
        }

        // Empêche la génération d'un rapport vide (toutes les PME ont au moins
        // déclaré un usage avant d'arriver sur le checkout via l'UI, mais on garde
        // un filet en cas d'appel direct).
        if ($organization->aiUsages()->count() === 0) {
            return redirect()
                ->route('usages.index')
                ->with('status', 'reports-need-usages');
        }

        $report = $organization->reports()->create([
            'snapshot' => $builder->build($organization),
        ]);

        // Mode dev / sans clé Stripe configurée : on simule le paiement
        // pour permettre de tester le flux PDF + historique sans frais.
        if (! config('services.stripe.secret')) {
            $report->update(['paid_at' => now()]);

            return redirect()
                ->route('reports.show', $report)
                ->with('status', 'report-fake-paid');
        }

        $stripe = new StripeClient(config('services.stripe.secret'));
        $session = $stripe->checkout->sessions->create([
            'mode' => 'payment',
            'line_items' => [[
                'price_data' => [
                    'currency' => config('services.stripe.currency', 'eur'),
                    'unit_amount' => (int) config('services.stripe.report_price', 4900),
                    'product_data' => [
                        'name' => 'Rapport de conformité AI Act',
                        'description' => 'Audit de conformité pour '.$organization->name,
                    ],
                ],
                'quantity' => 1,
            ]],
            'success_url' => route('checkout.success', ['report' => $report->id]).'&session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('checkout.cancel', ['report' => $report->id]),
        ]);

        $report->update(['stripe_session_id' => $session->id]);

        return redirect()->away($session->url);
    }

    public function success(Request $request, Report $report): RedirectResponse
    {
        $this->authorizeReport($request, $report);

        $sessionId = $request->query('session_id');
        if ($sessionId && config('services.stripe.secret')) {
            try {
                $stripe = new StripeClient(config('services.stripe.secret'));
                $session = $stripe->checkout->sessions->retrieve($sessionId);

                if ($session->payment_status !== 'paid') {
                    return redirect()
                        ->route('reports.index')
                        ->with('status', 'payment-not-confirmed');
                }
            } catch (\Throwable $e) {
                Log::warning('Stripe session retrieval failed', ['error' => $e->getMessage()]);

                return redirect()
                    ->route('reports.index')
                    ->with('status', 'payment-verification-failed');
            }
        }

        if (! $report->isPaid()) {
            $report->update(['paid_at' => now()]);
        }

        return redirect()
            ->route('reports.show', $report)
            ->with('status', 'report-paid');
    }

    public function cancel(Request $request, Report $report): RedirectResponse
    {
        $this->authorizeReport($request, $report);

        return redirect()
            ->route('reports.index')
            ->with('status', 'payment-cancelled');
    }

    /**
     * Garde-fou : un user ne peut accéder qu'aux reports de son organisation.
     * On ne passe pas par une Policy ici car le contrôleur est court et le
     * lien est direct.
     */
    private function authorizeReport(Request $request, Report $report): void
    {
        $organization = $request->user()->organization;
        abort_unless($organization !== null && $report->organization_id === $organization->id, 403);
    }
}
