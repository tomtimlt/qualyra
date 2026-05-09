<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\AiUsage;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreQuestionnaireRequest extends FormRequest
{
    /**
     * L'autorisation est doublée par le middleware can:update sur la route,
     * mais on garde un filet ici pour bloquer toute exécution directe.
     */
    public function authorize(): bool
    {
        $aiUsage = $this->route('aiUsage');
        $organization = $this->user()?->organization;

        if (! $aiUsage instanceof AiUsage || $organization === null) {
            return false;
        }

        return $aiUsage->organization_id === $organization->id;
    }

    /**
     * Construit dynamiquement les règles à partir de config/questionnaire.php
     * pour le type de l'AiUsage courant. Les valeurs sont scopées sous `answers`
     * pour faciliter le binding du formulaire (`name="answers[finality]"`).
     */
    public function rules(): array
    {
        $aiUsage = $this->route('aiUsage');
        $questions = $this->questionsFor($aiUsage);

        $rules = [
            'answers' => ['required', 'array'],
        ];

        foreach ($questions as $question) {
            $field = "answers.{$question['key']}";
            $fieldRules = $question['required'] ?? false
                ? ['required', 'string']
                : ['nullable', 'string'];

            if (in_array($question['type'], ['radio', 'select'], true)
                && ! empty($question['options'])) {
                $fieldRules[] = Rule::in(array_keys($question['options']));
            }

            if ($question['type'] === 'textarea') {
                $fieldRules[] = 'max:2000';
            } else {
                $fieldRules[] = 'max:255';
            }

            $rules[$field] = $fieldRules;
        }

        return $rules;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function questionsFor(AiUsage $aiUsage): array
    {
        $config = config('questionnaire');

        return [
            ...($config['common'] ?? []),
            ...($config[$aiUsage->type] ?? []),
        ];
    }
}
