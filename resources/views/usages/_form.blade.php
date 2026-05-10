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
    <x-input-label for="name" value="Nom de l'usage *" />
    <x-text-input id="name" name="name" type="text" required autofocus
                  :value="old('name', $aiUsage?->name)" style="margin-top: 6px" />
    <p class="help">Donnez un nom court et reconnaissable. Ex : « Scoring CV », « Génération offres commerciales (Mistral) ».</p>
    <x-input-error :messages="$errors->get('name')" />
</div>

<div>
    <x-input-label for="description" value="Description (facultative)" />
    <textarea id="description" name="description" rows="3" class="input textarea" style="margin-top: 6px"
              placeholder="Ex : utilisé par le service RH pour pré-trier les CV.">{{ old('description', $aiUsage?->description) }}</textarea>
    <x-input-error :messages="$errors->get('description')" />
</div>

<div class="form-grid">
    <div>
        <x-input-label for="type" value="Type d'IA *" />
        <select id="type" name="type" required class="input" style="margin-top: 6px">
            <option value="">— Sélectionner —</option>
            @foreach ($types as $value => $label)
                <option value="{{ $value }}" @selected(old('type', $aiUsage?->type) === $value)>{{ $label }}</option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('type')" />
    </div>

    <div>
        <x-input-label for="domain" value="Domaine d'usage *" />
        <select id="domain" name="domain" required class="input" style="margin-top: 6px">
            <option value="">— Sélectionner —</option>
            @foreach ($domains as $value => $label)
                <option value="{{ $value }}" @selected(old('domain', $aiUsage?->domain) === $value)>{{ $label }}</option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('domain')" />
    </div>
</div>
