<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\AiUsage;
use App\Services\AiActClassifier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class AssessmentController extends Controller implements HasMiddleware
{
    /**
     * Lancer une évaluation modifie l'état d'un AiUsage : on réutilise donc
     * `can:update,aiUsage` (AiUsagePolicy) — isolation tenant garantie.
     */
    public static function middleware(): array
    {
        return [
            new Middleware('can:update,aiUsage'),
        ];
    }

    public function store(AiUsage $aiUsage, AiActClassifier $classifier): RedirectResponse
    {
        // Pas d'évaluation possible sans questionnaire renseigné — on protège
        // l'utilisateur contre une classification "RISQUE_MINIMAL" trompeuse
        // qui ne refléterait que l'absence de données.
        if (! $aiUsage->responses()->exists()) {
            return redirect()
                ->route('usages.questionnaire.show', $aiUsage)
                ->with('status', 'questionnaire-required');
        }

        $classifier->persist($aiUsage);

        return redirect()
            ->route('usages.show', $aiUsage)
            ->with('status', 'assessment-computed');
    }
}
