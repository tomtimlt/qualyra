<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Créer mon organisation
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow sm:rounded-lg p-6">
                <p class="text-sm text-gray-600 mb-6">
                    Renseignez les informations de votre PME. Ces informations sont utilisées
                    uniquement pour l'audit de conformité AI Act + RGPD.
                </p>

                <form method="POST" action="{{ route('organization.store') }}" class="space-y-6">
                    @csrf

                    <div>
                        <x-input-label for="name" value="Nom de l'organisation" />
                        <x-text-input id="name" name="name" type="text" required autofocus
                                      class="mt-1 block w-full" :value="old('name')" />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="siret" value="SIRET (facultatif)" />
                        <x-text-input id="siret" name="siret" type="text" maxlength="14"
                                      class="mt-1 block w-full" :value="old('siret')"
                                      placeholder="14 chiffres" />
                        <x-input-error :messages="$errors->get('siret')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="size" value="Taille de l'organisation" />
                        <select id="size" name="size" required
                                class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            <option value="">— Sélectionner —</option>
                            @foreach (['1-19', '20-49', '50-149', '150+'] as $sizeOption)
                                <option value="{{ $sizeOption }}" @selected(old('size') === $sizeOption)>
                                    {{ $sizeOption }} salariés
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('size')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="sector" value="Secteur d'activité (facultatif)" />
                        <x-text-input id="sector" name="sector" type="text"
                                      class="mt-1 block w-full" :value="old('sector')"
                                      placeholder="Ex : Santé, Industrie, RH..." />
                        <x-input-error :messages="$errors->get('sector')" class="mt-2" />
                    </div>

                    <div class="flex items-center justify-end gap-3">
                        <a href="{{ route('dashboard') }}"
                           class="text-sm text-gray-600 hover:text-gray-800 underline">Annuler</a>
                        <x-primary-button>Créer l'organisation</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
