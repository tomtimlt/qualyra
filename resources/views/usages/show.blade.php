<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $aiUsage->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status') === 'questionnaire-saved')
                <div class="rounded-md bg-green-50 p-4 border border-green-200 text-sm text-green-800">
                    Réponses du questionnaire enregistrées.
                </div>
            @elseif (session('status') === 'assessment-computed')
                <div class="rounded-md bg-green-50 p-4 border border-green-200 text-sm text-green-800">
                    Classification AI Act calculée.
                </div>
            @endif

            <div class="bg-white shadow sm:rounded-lg p-6 space-y-4">
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                    <div>
                        <dt class="font-medium text-gray-500">Type d'IA</dt>
                        <dd class="mt-1 text-gray-900">{{ $aiUsage->type }}</dd>
                    </div>
                    <div>
                        <dt class="font-medium text-gray-500">Domaine</dt>
                        <dd class="mt-1 text-gray-900">{{ $aiUsage->domain }}</dd>
                    </div>
                    <div class="sm:col-span-2">
                        <dt class="font-medium text-gray-500">Description</dt>
                        <dd class="mt-1 text-gray-900 whitespace-pre-line">
                            {{ $aiUsage->description ?? '—' }}
                        </dd>
                    </div>
                </dl>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200">
                    <a href="{{ route('usages.index') }}"
                       class="text-sm text-gray-600 hover:text-gray-800 underline">Retour à la liste</a>
                    <a href="{{ route('usages.edit', $aiUsage) }}"
                       class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition">
                        Modifier
                    </a>
                </div>
            </div>

            <div class="bg-white shadow sm:rounded-lg p-6 flex items-center justify-between">
                <div>
                    <h3 class="font-semibold text-gray-800">Questionnaire AI Act</h3>
                    <p class="text-sm text-gray-600 mt-1">
                        @if ($aiUsage->responses()->exists())
                            Questionnaire renseigné — vous pouvez modifier vos réponses.
                        @else
                            Pas encore renseigné. Répondez aux questions pour préparer la classification.
                        @endif
                    </p>
                </div>
                <a href="{{ route('usages.questionnaire.show', $aiUsage) }}"
                   class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition">
                    {{ $aiUsage->responses()->exists() ? 'Modifier' : 'Répondre' }}
                </a>
            </div>

            @php
                $assessment = $aiUsage->assessments()->latest('computed_at')->first();
                $niveauStyles = [
                    'INACCEPTABLE' => ['bg' => 'bg-red-50', 'border' => 'border-red-300', 'text' => 'text-red-900', 'badge' => 'bg-red-600'],
                    'HAUT_RISQUE' => ['bg' => 'bg-orange-50', 'border' => 'border-orange-300', 'text' => 'text-orange-900', 'badge' => 'bg-orange-600'],
                    'RISQUE_LIMITE' => ['bg' => 'bg-yellow-50', 'border' => 'border-yellow-300', 'text' => 'text-yellow-900', 'badge' => 'bg-yellow-600'],
                    'RISQUE_MINIMAL' => ['bg' => 'bg-green-50', 'border' => 'border-green-300', 'text' => 'text-green-900', 'badge' => 'bg-green-600'],
                ];
                $niveauLabels = [
                    'INACCEPTABLE' => 'Risque inacceptable',
                    'HAUT_RISQUE' => 'Haut risque',
                    'RISQUE_LIMITE' => 'Risque limité',
                    'RISQUE_MINIMAL' => 'Risque minimal',
                ];
            @endphp

            <div class="bg-white shadow sm:rounded-lg p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-semibold text-gray-800">Classification AI Act</h3>
                    @if ($aiUsage->responses()->exists())
                        <form method="POST" action="{{ route('usages.assessment.store', $aiUsage) }}">
                            @csrf
                            <button type="submit"
                                    class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition">
                                {{ $assessment ? 'Recalculer' : 'Évaluer' }}
                            </button>
                        </form>
                    @endif
                </div>

                @if (! $assessment)
                    <p class="text-sm text-gray-500">
                        Pas encore évalué. Renseignez le questionnaire puis cliquez sur « Évaluer ».
                    </p>
                @else
                    @php $style = $niveauStyles[$assessment->niveau] ?? $niveauStyles['RISQUE_MINIMAL']; @endphp
                    <div class="rounded-md border {{ $style['bg'] }} {{ $style['border'] }} p-4 space-y-3">
                        <div class="flex items-center gap-3">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold uppercase tracking-wider text-white {{ $style['badge'] }}">
                                {{ $niveauLabels[$assessment->niveau] ?? $assessment->niveau }}
                            </span>
                            <span class="text-xs text-gray-600">{{ $assessment->article }}</span>
                        </div>
                        <p class="text-sm {{ $style['text'] }}">{{ $assessment->raison }}</p>
                        <p class="text-xs text-gray-500">
                            Calculée le {{ $assessment->computed_at->format('d/m/Y à H:i') }}
                            · Règle : {{ $assessment->regle_id }}
                        </p>
                    </div>

                    @if (! empty($assessment->alertes))
                        <div class="mt-4">
                            <h4 class="text-sm font-semibold text-gray-700 mb-2">Alertes complémentaires</h4>
                            <ul class="space-y-2">
                                @foreach ($assessment->alertes as $alerte)
                                    <li class="text-sm text-gray-700 bg-amber-50 border border-amber-200 rounded p-3">
                                        <span class="font-mono text-xs text-amber-800">{{ $alerte['code'] }}</span>
                                        — {{ $alerte['message'] }}
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
