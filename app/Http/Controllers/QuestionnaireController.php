<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreQuestionnaireRequest;
use App\Models\AiUsage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\View\View;

class QuestionnaireController extends Controller implements HasMiddleware
{
    /**
     * L'accès au questionnaire (lecture comme écriture) est conditionné à
     * la capacité de modifier l'AiUsage cible — réutilise AiUsagePolicy::update
     * et garantit l'isolation tenant.
     */
    public static function middleware(): array
    {
        return [
            new Middleware('can:update,aiUsage'),
        ];
    }

    public function show(AiUsage $aiUsage): View
    {
        $questions = $this->questionsFor($aiUsage);
        $answers = $aiUsage->responses()->pluck('variable_value', 'variable_key')->toArray();

        return view('questionnaire.show', [
            'aiUsage' => $aiUsage,
            'questions' => $questions,
            'answers' => $answers,
        ]);
    }

    public function store(StoreQuestionnaireRequest $request, AiUsage $aiUsage): RedirectResponse
    {
        $answers = $request->validated()['answers'];
        $allowedKeys = collect($this->questionsFor($aiUsage))->pluck('key')->all();

        // Filet supplémentaire : on ne persiste que les clés déclarées dans la
        // config pour ce type. Empêche toute injection de variable_key inattendue
        // via du payload non couvert par les règles (ex: future évolution de la config).
        $filtered = array_intersect_key($answers, array_flip($allowedKeys));

        foreach ($filtered as $key => $value) {
            $aiUsage->responses()->updateOrCreate(
                ['variable_key' => $key],
                ['variable_value' => (string) $value],
            );
        }

        return redirect()
            ->route('usages.show', $aiUsage)
            ->with('status', 'questionnaire-saved');
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function questionsFor(AiUsage $aiUsage): array
    {
        $config = config('questionnaire');

        return [
            ...($config['common'] ?? []),
            ...($config[$aiUsage->type] ?? []),
        ];
    }
}
