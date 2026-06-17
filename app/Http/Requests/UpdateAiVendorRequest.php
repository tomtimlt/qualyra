<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAiVendorRequest extends FormRequest
{
    /**
     * Autorisation gérée par AiVendorPolicy::update via middleware controller.
     */
    public function authorize(): bool
    {
        return true;
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

    protected function prepareForValidation(): void
    {
        $this->merge([
            'hors_ue' => $this->boolean('hors_ue'),
            'declaration_conformite_art47' => $this->boolean('declaration_conformite_art47'),
            'dpa_art28_signe' => $this->boolean('dpa_art28_signe'),
        ]);
    }
}
