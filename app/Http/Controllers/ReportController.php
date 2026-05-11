<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Spatie\Browsershot\Browsershot;

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

    public function download(Request $request, Report $report): Response
    {
        $this->authorizeReport($request, $report);

        $html = view('reports.pdf', ['report' => $report])->render();

        $browsershot = Browsershot::html($html)
            ->setNodeModulePath(base_path('node_modules'))
            ->showBackground();

        if (PHP_OS_FAMILY === 'Darwin') {
            $browsershot->setChromePath('/Applications/Google Chrome.app/Contents/MacOS/Google Chrome');
        } else {
            $browsershot->setChromePath('/usr/bin/chromium')
                ->noSandbox();
        }

        $pdf = $browsershot->pdf();

        return response($pdf)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="rapport-ai-act-'.$report->id.'.pdf"');
    }

    private function authorizeReport(Request $request, Report $report): void
    {
        $organization = $request->user()->organization;
        abort_unless($organization !== null && $report->organization_id === $organization->id, 403);
    }
}
