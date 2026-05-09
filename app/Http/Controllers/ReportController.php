<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Report;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(Request $request): View|RedirectResponse
    {
        $organization = $request->user()->organization;
        if ($organization === null) {
            return redirect()->route('organization.create');
        }

        $reports = $organization->reports()->latest()->get();

        return view('reports.index', compact('reports'));
    }

    public function show(Request $request, Report $report): View
    {
        $this->authorizeReport($request, $report);

        return view('reports.show', compact('report'));
    }

    /**
     * Téléchargement du PDF — verrouillé tant que le report n'est pas payé.
     * C'est le seul gate de monétisation : l'utilisateur peut consulter
     * le récapitulatif HTML mais le PDF officiel exige le paiement.
     */
    public function download(Request $request, Report $report): Response
    {
        $this->authorizeReport($request, $report);
        abort_unless($report->isPaid(), 402, 'Paiement requis pour télécharger ce rapport.');

        $pdf = Pdf::loadView('reports.pdf', ['report' => $report]);

        return $pdf->download("rapport-ai-act-{$report->id}.pdf");
    }

    private function authorizeReport(Request $request, Report $report): void
    {
        $organization = $request->user()->organization;
        abort_unless($organization !== null && $report->organization_id === $organization->id, 403);
    }
}
