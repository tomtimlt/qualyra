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
        $questions = $this->questionsFor($aiUsage);
        $allowedKeys = collect($questions)->pluck('key')->all();

        // Filet supplémentaire : on ne persiste que les clés déclarées dans la
        // config pour ce type. Empêche toute injection de variable_key inattendue
        // via du payload non couvert par les règles (ex: future évolution de la config).
        $filtered = array_intersect_key($answers, array_flip($allowedKeys));

        // Index des types de questions pour savoir quoi sérialiser :
        // les checkbox arrivent en array et sont stockés en CSV scalaire
        // (compatible colonne string + contrainte unique (ai_usage_id, key)).
        $typesByKey = collect($questions)->pluck('type', 'key')->all();

        foreach ($filtered as $key => $value) {
            $serialized = ($typesByKey[$key] ?? null) === 'checkbox' && is_array($value)
                ? implode(',', array_values(array_filter($value, 'is_string')))
                : (string) $value;

            $aiUsage->responses()->updateOrCreate(
                ['variable_key' => $key],
                ['variable_value' => $serialized],
            );
        }

        return redirect()
            ->route('usages.show', $aiUsage)
            ->with('status', 'questionnaire-saved');
    }

    /**
     * Construit la liste des questions à poser pour cet usage IA en fusionnant :
     *   - les questions communes (TYPE/DOM/DEC/PUB/DIFF/DATA/CTRL...)
     *   - les questions spécifiques au type (LLM_GEN, IA_GEN, IA_BIO...)
     *   - les questions spécifiques au domaine (RH, EDUCATION, SANTE...)
     *
     * @return array<int, array<string, mixed>>
     */
    private function questionsFor(AiUsage $aiUsage): array
    {
        $config = config('questionnaire');

        return [
            ...($config['common'] ?? []),
            ...($config['types'][$aiUsage->type] ?? []),
            ...($config['domains'][$aiUsage->domain] ?? []),
        ];
    }
}
