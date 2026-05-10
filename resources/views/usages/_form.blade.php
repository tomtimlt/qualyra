{{-- Champs partagés entre la création et l'édition d'un usage IA --}}
@php
    $aiUsage ??= null;
    $types = [
        'LLM_GEN' => 'LLM générique (ChatGPT, Claude...)',
        'IA_GEN' => 'IA générative (image, audio, vidéo)',
        'IA_SCORING' => 'IA de scoring/notation',
        'IA_BIO' => 'IA biométrique',
        'AUTRE' => 'Autre',
    ];
    $domains = [
        'RH' => 'Ressources humaines',
        'EDUCATION' => 'Éducation',
        'CREDIT' => 'Crédit / scoring financier',
        'SANTE' => 'Santé',
        'SECURITE' => 'Sécurité / surveillance',
        'MARKETING' => 'Marketing',
        'PROD_INT' => 'Productivité interne',
        'DEV_LOG' => 'Développement logiciel',
        'AUTRE' => 'Autre',
    ];
@endphp

<div>
    <x-input-label for="name" value="Nom de l'usage" />
    <x-text-input id="name" name="name" type="text" required autofocus
                  class="mt-1 block w-full"
                  :value="old('name', $aiUsage?->name)" />
    <x-input-error :messages="$errors->get('name')" class="mt-2" />
</div>

<div>
    <x-input-label for="description" value="Description (facultative)" />
    <textarea id="description" name="description" rows="3"
              class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
              placeholder="Ex : utilisé par le service RH pour pré-trier les CV.">{{ old('description', $aiUsage?->description) }}</textarea>
    <x-input-error :messages="$errors->get('description')" class="mt-2" />
</div>

<div>
    <x-input-label for="type" value="Type d'IA" />
    <select id="type" name="type" required
            class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
        <option value="">— Sélectionner —</option>
        @foreach ($types as $value => $label)
            <option value="{{ $value }}" @selected(old('type', $aiUsage?->type) === $value)>{{ $label }}</option>
        @endforeach
    </select>
    <x-input-error :messages="$errors->get('type')" class="mt-2" />
</div>

<div>
    <x-input-label for="domain" value="Domaine d'usage" />
    <select id="domain" name="domain" required
            class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
        <option value="">— Sélectionner —</option>
        @foreach ($domains as $value => $label)
            <option value="{{ $value }}" @selected(old('domain', $aiUsage?->domain) === $value)>{{ $label }}</option>
        @endforeach
    </select>
    <x-input-error :messages="$errors->get('domain')" class="mt-2" />
</div>
