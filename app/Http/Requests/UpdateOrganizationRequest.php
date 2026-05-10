<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateOrganizationRequest extends FormRequest
{
    /**
     * L'utilisateur doit être authentifié et propriétaire d'une organisation.
     * Pas d'identifiant dans l'URL : la mise à jour cible toujours
     * $user->organization. Cela évite tout vecteur IDOR sur les organisations
     * d'autres utilisateurs.
     */
    public function authorize(): bool
    {
        $user = $this->user();

        return $user !== null && $user->organization !== null;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $orgId = $this->user()?->organization?->id;

        return [
            'name' => ['required', 'string', 'max:255'],
            'siret' => [
                'nullable',
                'string',
                'regex:/^[0-9]{14}$/',
                Rule::unique('organizations', 'siret')->ignore($orgId),
            ],
            'size' => ['required', Rule::in(['1-19', '20-49', '50-149', '150+'])],
            'sector' => ['nullable', 'string', 'max:255'],
        ];
    }

    /**
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
