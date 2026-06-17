{{-- Champs partagés entre la création et l'édition d'un fournisseur IA --}}
@php
    $vendor ??= null;
    $types = [
        'INTERNE' => 'Interne / auto-hébergé',
        'SAAS' => 'SaaS (multi-tenant)',
        'API_PUBLIC' => 'API publique (clé)',
        'OPEN_SOURCE' => 'Open source exécuté localement',
    ];
@endphp

<div>
    <x-input-label for="name" value="Nom du fournisseur *" />
    <x-text-input id="name" name="name" type="text" required autofocus
                  :value="old('name', $vendor?->name)" style="margin-top: 6px" />
    <p class="help">Nom commercial du fournisseur ou solution. Ex : « OpenAI », « Mistral AI », « Solution interne — Data Lab ».</p>
    <x-input-error :messages="$errors->get('name')" />
</div>

<div class="form-grid">
    <div>
        <x-input-label for="type_contractuel" value="Type contractuel *" />
        <select id="type_contractuel" name="type_contractuel" required class="input" style="margin-top: 6px">
            <option value="">— Sélectionner —</option>
            @foreach ($types as $value => $label)
                <option value="{{ $value }}" @selected(old('type_contractuel', $vendor?->type_contractuel) === $value)>{{ $label }}</option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('type_contractuel')" />
    </div>

    <div>
        <x-input-label for="pays_hebergement" value="Pays d'hébergement (ISO-2)" />
        <x-text-input id="pays_hebergement" name="pays_hebergement" type="text" maxlength="2"
                      placeholder="FR" :value="old('pays_hebergement', $vendor?->pays_hebergement)" style="margin-top: 6px" />
        <p class="help">Code pays à 2 lettres du centre de traitement principal (FR, US, IE, DE...).</p>
        <x-input-error :messages="$errors->get('pays_hebergement')" />
    </div>
</div>

<div class="form-checks">
    <label class="check">
        <input type="checkbox" name="hors_ue" value="1" @checked(old('hors_ue', $vendor?->hors_ue))>
        <span>Données traitées hors Union européenne</span>
    </label>

    <label class="check">
        <input type="checkbox" name="declaration_conformite_art47" value="1" @checked(old('declaration_conformite_art47', $vendor?->declaration_conformite_art47))>
        <span>Déclaration de conformité fournisseur (Art. 47 AI Act) reçue</span>
    </label>

    <label class="check">
        <input type="checkbox" name="dpa_art28_signe" value="1" @checked(old('dpa_art28_signe', $vendor?->dpa_art28_signe))>
        <span>Contrat de sous-traitance RGPD (Art. 28) signé</span>
    </label>

    <label class="check">
        <input type="checkbox" name="cct_signees" value="1" @checked(old('cct_signees', $vendor?->cct_signees))>
        <span>Clauses contractuelles types signées (transfert hors UE, Décision UE 2021/914)</span>
    </label>
</div>

<div>
    <x-input-label for="notes" value="Notes internes (facultatif)" />
    <textarea id="notes" name="notes" rows="3" class="input textarea" style="margin-top: 6px"
              placeholder="Précisions sur le contrat, références, contacts...">{{ old('notes', $vendor?->notes) }}</textarea>
    <x-input-error :messages="$errors->get('notes')" />
</div>
