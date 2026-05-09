<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreOrganizationRequest extends FormRequest
{
    /**
     * L'utilisateur doit être authentifié et ne pas déjà avoir
     * d'organisation rattachée à son compte (1 user = 1 PME).
     */
    public function authorize(): bool
    {
        $user = $this->user();

        return $user !== null && $user->organization === null;
    }

    /**
     * Règles de validation pour la création d'une organisation.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            // Format SIRET français + unicité : un même SIRET ne peut pas
            // être déclaré par deux PME différentes (cohérence métier).
            'siret' => ['nullable', 'string', 'regex:/^[0-9]{14}$/', Rule::unique('organizations', 'siret')],
            'size' => ['required', Rule::in(['1-19', '20-49', '50-149', '150+'])],
            'sector' => ['nullable', 'string', 'max:255'],
        ];
    }

    /**
     * Messages de validation en français.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Le nom de l\'organisation est obligatoire.',
            'siret.regex' => 'Le SIRET doit contenir exactement 14 chiffres.',
            'siret.unique' => 'Ce SIRET est déjà associé à une autre organisation.',
            'size.required' => 'La taille de l\'organisation est obligatoire.',
            'size.in' => 'La taille sélectionnée n\'est pas valide.',
        ];
    }
}
