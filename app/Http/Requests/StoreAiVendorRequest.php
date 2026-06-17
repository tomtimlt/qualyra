<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAiVendorRequest extends FormRequest
{
    /**
     * Refuse 403 si l'utilisateur n'a pas encore créé son organisation.
     * Empêche tout rattachement vendor à une org fantôme.
     */
    public function authorize(): bool
    {
        return $this->user()?->organization !== null;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'type_contractuel' => ['required', Rule::in(['INTERNE', 'SAAS', 'API_PUBLIC', 'OPEN_SOURCE'])],
            'pays_hebergement' => ['nullable', 'string', 'size:2'],
            'hors_ue' => ['sometimes', 'boolean'],
            'declaration_conformite_art47' => ['sometimes', 'boolean'],
            'dpa_art28_signe' => ['sometimes', 'boolean'],
            'cct_signees' => ['nullable', 'boolean'],
            'notes' => ['nullable', 'string', 'max:5000'],
        ];
    }

    /**
     * Normalise les checkboxes HTML (absent → false) avant la validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'hors_ue' => $this->boolean('hors_ue'),
            'declaration_conformite_art47' => $this->boolean('declaration_conformite_art47'),
            'dpa_art28_signe' => $this->boolean('dpa_art28_signe'),
        ]);
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Le nom du fournisseur est obligatoire.',
            'type_contractuel.required' => 'Le type contractuel est obligatoire.',
            'type_contractuel.in' => 'Le type contractuel sélectionné n\'est pas valide.',
            'pays_hebergement.size' => 'Le code pays doit faire 2 lettres (ex : FR, US, IE).',
        ];
    }
}
