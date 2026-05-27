<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAiUsageRequest extends FormRequest
{
    /**
     * Refuse 403 si l'utilisateur n'a pas encore créé son organisation
     * (sécurité : empêche le rattachement d'un usage à une org fantôme).
     */
    public function authorize(): bool
    {
        return $this->user()?->organization !== null;
    }

    /**
     * Règles de validation pour la création d'un usage IA.
     *
     * Les enums sont alignés sur la migration ai_usages
     * (cf. database/migrations/...create_ai_usages_table.php).
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $organizationId = $this->user()?->organization?->id;

        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'type' => ['required', Rule::in(['LLM_GEN', 'IA_GEN', 'IA_SCORING', 'IA_BIO', 'AUTRE'])],
            'domain' => ['required', Rule::in(['RH', 'EDUCATION', 'CREDIT', 'SANTE', 'SECURITE', 'MARKETING', 'PROD_INT', 'DEV_LOG', 'AUTRE'])],
            // ai_vendor_id : optionnel + scopé à l'organisation du user pour
            // empêcher tout cross-tenant via valeur forgée en POST.
            'ai_vendor_id' => [
                'nullable',
                'integer',
                Rule::exists('ai_vendors', 'id')->where('organization_id', $organizationId),
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Le nom de l\'usage est obligatoire.',
            'type.required' => 'Le type d\'IA est obligatoire.',
            'type.in' => 'Le type d\'IA sélectionné n\'est pas valide.',
            'domain.required' => 'Le domaine d\'usage est obligatoire.',
            'domain.in' => 'Le domaine sélectionné n\'est pas valide.',
        ];
    }
}
