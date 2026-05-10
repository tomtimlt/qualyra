<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Questionnaire AI Act — {{ $aiUsage->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="bg-white shadow sm:rounded-lg p-6">
                <p class="text-sm text-gray-600">
                    Répondez à ces questions pour permettre la classification de cet usage
                    selon les niveaux de risque définis par l'AI Act. Vos réponses sont sauvegardées
                    et peuvent être modifiées à tout moment.
                </p>
            </div>

            <form method="POST" action="{{ route('usages.questionnaire.store', $aiUsage) }}"
                  class="bg-white shadow sm:rounded-lg p-6 space-y-6">
                @csrf

                @foreach ($questions as $question)
                    @php
                        $key = $question['key'];
                        $name = "answers[{$key}]";
                        // Pour les checkbox (multi-valeur), la valeur est stockée en CSV
                        // côté BDD ; on l'éclate pour reconstruire le tableau attendu
                        // par les inputs et par old() en cas de re-rendu après erreur.
                        if (($question['type'] ?? null) === 'checkbox') {
                            $stored = $answers[$key] ?? '';
                            $defaults = $stored === '' ? [] : array_filter(explode(',', $stored));
                            $current = old("answers.{$key}", $defaults);
                            if (! is_array($current)) {
                                $current = [];
                            }
                        } else {
                            $current = old("answers.{$key}", $answers[$key] ?? null);
                        }
                    @endphp

                    <div>
                        <x-input-label :for="$key"
                            :value="$question['label'].($question['required'] ?? false ? ' *' : '')" />

                        @if (! empty($question['help']))
                            <p class="text-xs text-gray-500 mt-1">{{ $question['help'] }}</p>
                        @endif

                        @if ($question['type'] === 'textarea')
                            <textarea id="{{ $key }}" name="{{ $name }}" rows="3"
                                      class="mt-2 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                      @if ($question['required'] ?? false) required @endif
                            >{{ $current }}</textarea>

                        @elseif ($question['type'] === 'select')
                            <select id="{{ $key }}" name="{{ $name }}"
                                    class="mt-2 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                    @if ($question['required'] ?? false) required @endif>
                                <option value="">— Sélectionner —</option>
                                @foreach ($question['options'] as $value => $label)
                                    <option value="{{ $value }}" @selected($current === $value)>{{ $label }}</option>
                                @endforeach
                            </select>

                        @elseif ($question['type'] === 'radio')
                            <div class="mt-2 space-y-2">
                                @foreach ($question['options'] as $value => $label)
                                    <label class="flex items-center gap-2 text-sm text-gray-700">
                                        <input type="radio"
                                               name="{{ $name }}"
                                               value="{{ $value }}"
                                               @checked($current === $value)
                                               @if ($question['required'] ?? false) required @endif
                                               class="text-indigo-600 focus:ring-indigo-500 border-gray-300">
                                        {{ $label }}
                                    </label>
                                @endforeach
                            </div>

                        @elseif ($question['type'] === 'checkbox')
                            <div class="mt-2 space-y-2">
                                @foreach ($question['options'] as $value => $label)
                                    <label class="flex items-center gap-2 text-sm text-gray-700">
                                        <input type="checkbox"
                                               name="answers[{{ $key }}][]"
                                               value="{{ $value }}"
                                               @checked(in_array($value, $current, true))
                                               class="text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                        {{ $label }}
                                    </label>
                                @endforeach
                            </div>
                        @endif

                        <x-input-error :messages="$errors->get('answers.'.$key)" class="mt-2" />
                    </div>
                @endforeach

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200">
                    <a href="{{ route('usages.show', $aiUsage) }}"
                       class="text-sm text-gray-600 hover:text-gray-800 underline">Annuler</a>
                    <x-primary-button>
                        Enregistrer mes réponses
                    </x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
