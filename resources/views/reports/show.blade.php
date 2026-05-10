<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Rapport #{{ $report->id }}
        </h2>
    </x-slot>

    @php
        $snapshot = $report->snapshot;
        // Lecture du contenu rédactionnel figé. Fallback live pour les
        // rapports antérieurs à l'enrichissement (pas de clé `content`).
        $content = $snapshot['content']
            ?? app(\App\Services\ReportContentBuilder::class)->build($snapshot);
        $meta = $content['meta'];
        $compteurs = $content['compteurs_par_niveau'];
        $labels = $content['niveau_labels'];
        $niveauColors = [
            'INACCEPTABLE' => 'bg-red-700',
            'HAUT_RISQUE' => 'bg-orange-600',
            'RISQUE_LIMITE' => 'bg-yellow-600',
            'RISQUE_MINIMAL' => 'bg-green-700',
            'NON_EVALUE' => 'bg-gray-500',
        ];
        $boxClasses = [
            'FLAG_ZONE_GRISE' => 'border-amber-500 bg-amber-50',
            'AGGRAVATION' => 'border-red-600 bg-red-50',
        ];
    @endphp

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- En-tête + bouton de téléchargement --}}
            <div class="bg-white shadow sm:rounded-lg p-6 flex justify-between items-start">
                <div>
                    <p class="text-sm font-semibold text-gray-900">{{ $meta['nom_pme'] }}</p>
                    @if (! empty($meta['siret']))
                        <p class="text-xs text-gray-500">SIRET {{ $meta['siret'] }}</p>
                    @endif
                    <p class="text-xs text-gray-500 mt-1">
                        Date d'audit : {{ $meta['date_audit'] }} ·
                        {{ $meta['nb_usages_declares'] }} usage(s) déclaré(s)
                    </p>
                </div>
                <div>
                    @if ($report->isPaid())
                        <span class="inline-block px-3 py-1 rounded-full text-xs bg-green-100 text-green-800 font-semibold">Payé</span>
                        <a href="{{ route('reports.download', $report) }}"
                           class="ml-3 inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 transition">
                            Télécharger le PDF
                        </a>
                    @else
                        <span class="inline-block px-3 py-1 rounded-full text-xs bg-amber-100 text-amber-800 font-semibold">En attente de paiement</span>
                    @endif
                </div>
            </div>

            {{-- Niveau de risque global + compteurs --}}
            <div class="bg-white shadow sm:rounded-lg p-6">
                <h3 class="font-semibold text-gray-900 mb-2">Niveau de risque global</h3>
                <p class="text-sm text-gray-700 mb-4 italic">{{ $content['niveau_risque_global'] }}</p>

                <div class="grid grid-cols-5 gap-3">
                    @foreach (['INACCEPTABLE', 'HAUT_RISQUE', 'RISQUE_LIMITE', 'RISQUE_MINIMAL', 'NON_EVALUE'] as $niveau)
                        <div class="border border-gray-200 rounded-md p-3 text-center">
                            <div class="text-2xl font-bold text-gray-900">{{ $compteurs[$niveau] ?? 0 }}</div>
                            <div class="text-xs text-gray-500 uppercase mt-1">{{ $labels[$niveau] }}</div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Synthèse exécutive --}}
            <div class="bg-white shadow sm:rounded-lg p-6 space-y-3">
                <h3 class="font-semibold text-gray-900">Synthèse exécutive</h3>
                <p class="text-sm text-gray-700">{{ $content['synthese_executive']['header'] }}</p>
                <p class="text-sm text-gray-700">{{ $content['synthese_executive']['repartition'] }}</p>

                <div class="border-l-4 border-amber-500 bg-amber-50 p-4">
                    <p class="text-sm text-gray-800">{{ $content['synthese_executive']['sanctions'] }}</p>
                </div>

                <h4 class="font-semibold text-gray-800 mt-2">Trois priorités d'action</h4>
                <ol class="list-decimal list-inside space-y-1 text-sm text-gray-700">
                    @foreach ($content['priorites'] as $priorite)
                        <li class="font-semibold">{{ $priorite }}</li>
                    @endforeach
                </ol>
            </div>

            {{-- Détail par usage --}}
            <div class="bg-white shadow sm:rounded-lg p-6 space-y-5">
                <h3 class="font-semibold text-gray-900">Analyse détaillée par cas d'usage</h3>

                @forelse ($content['usages'] as $i => $usage)
                    <div class="border border-gray-200 rounded-md p-4 space-y-3">
                        <div class="flex items-center justify-between">
                            <h4 class="font-semibold text-gray-900">{{ $i + 1 }}. {{ $usage['name'] }}</h4>
                            <span class="inline-block px-2 py-1 rounded text-xs text-white {{ $niveauColors[$usage['niveau']] ?? 'bg-gray-500' }}">
                                {{ $usage['niveau_label'] }}
                            </span>
                        </div>
                        <p class="text-xs text-gray-500">
                            Type : {{ $usage['type'] }} · Domaine : {{ $usage['domain'] }}
                            @if ($usage['regle_id']) · Règle : {{ $usage['regle_id'] }} @endif
                            @if ($usage['article']) · {{ $usage['article'] }} @endif
                        </p>

                        @if ($usage['raison'])
                            <div>
                                <p class="text-xs font-semibold text-gray-700 uppercase">Justification</p>
                                <p class="text-sm text-gray-700 mt-1">{{ $usage['raison'] }}</p>
                            </div>
                        @endif

                        <div>
                            <p class="text-xs font-semibold text-gray-700 uppercase">Analyse réglementaire</p>
                            <p class="text-sm text-gray-700 mt-1">{{ $usage['paragraphe_niveau'] }}</p>
                        </div>

                        @if (! empty($usage['encadres']))
                            <div>
                                <p class="text-xs font-semibold text-gray-700 uppercase mb-2">Obligations applicables</p>
                                <div class="space-y-2">
                                    @foreach ($usage['encadres'] as $encadre)
                                        <div class="border-l-4 border-blue-500 bg-blue-50 p-3">
                                            <p class="text-xs font-bold text-gray-900">{{ $encadre['titre'] }}</p>
                                            <p class="text-xs text-gray-700 mt-1">{{ $encadre['contenu'] }}</p>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        @if (! empty($usage['alertes']))
                            <div>
                                <p class="text-xs font-semibold text-gray-700 uppercase mb-2">Alertes complémentaires</p>
                                <div class="space-y-2">
                                    @foreach ($usage['alertes'] as $alerte)
                                        @php $cls = $boxClasses[$alerte['type'] ?? ''] ?? 'border-gray-400 bg-gray-50'; @endphp
                                        <div class="border-l-4 {{ $cls }} p-3">
                                            <p class="text-xs font-bold text-gray-900">
                                                {{ $alerte['code'] ?? 'alerte' }}
                                                @if (! empty($alerte['type']))
                                                    <span class="text-xs text-gray-500 font-normal">— {{ $alerte['type'] }}</span>
                                                @endif
                                            </p>
                                            <p class="text-xs text-gray-700 mt-1">{{ $alerte['message'] ?? '' }}</p>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                @empty
                    <p class="text-sm text-gray-500">Aucun usage déclaré.</p>
                @endforelse
            </div>

            {{-- Plan d'action 30/60/90 --}}
            <div class="bg-white shadow sm:rounded-lg p-6 space-y-3">
                <h3 class="font-semibold text-gray-900">Plan d'action stratégique 30 / 60 / 90 jours</h3>
                <p class="text-sm text-gray-700">{{ $content['plan_action']['header'] }}</p>

                @foreach (['phase_30j' => '30 jours — P0', 'phase_60j' => '60 jours — P1', 'phase_90j' => '90 jours — P2'] as $key => $titre)
                    <div class="mt-3">
                        <h4 class="font-semibold text-gray-800">{{ $titre }}</h4>
                        <p class="text-xs text-gray-600 italic">{{ $content['plan_action'][$key]['intro'] }}</p>
                        <ul class="mt-2 space-y-2">
                            @forelse ($content['plan_action'][$key]['actions'] as $action)
                                <li class="border-l-4 border-blue-500 bg-blue-50 p-3">
                                    <div class="text-sm font-semibold text-gray-900">{{ $action['titre'] }}</div>
                                    <p class="text-sm text-gray-700 mt-1">{{ $action['contenu'] }}</p>
                                    <p class="text-xs text-gray-600 mt-1">
                                        Responsable : <strong>{{ $action['responsable'] }}</strong> · Effort : {{ $action['effort'] }}
                                    </p>
                                </li>
                            @empty
                                <li class="text-xs text-gray-500 italic">Aucune action déclenchée pour cette phase.</li>
                            @endforelse
                        </ul>
                    </div>
                @endforeach
            </div>

            {{-- Checklist --}}
            <div class="bg-white shadow sm:rounded-lg p-6">
                <h3 class="font-semibold text-gray-900 mb-3">Checklist finale opérationnelle</h3>
                <ul class="space-y-2">
                    @foreach ($content['checklist'] as $i => $item)
                        <li class="flex items-start gap-2 text-sm text-gray-700">
                            <input type="checkbox" disabled class="mt-1">
                            <span><strong>{{ $i + 1 }}. {{ $item['point'] }}</strong> — {{ $item['description'] }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>

            {{-- Zones grises --}}
            <div class="bg-white shadow sm:rounded-lg p-6 space-y-3">
                <h3 class="font-semibold text-gray-900">Zones grises juridiques</h3>
                <p class="text-sm text-gray-700">{{ $content['zones_grises']['intro'] }}</p>
                <div class="border-l-4 border-gray-400 bg-gray-50 p-3">
                    <p class="text-xs font-bold text-gray-900">Calendrier AI Act et « Digital Omnibus »</p>
                    <p class="text-xs text-gray-700 mt-1">{{ $content['zones_grises']['digital_omnibus'] }}</p>
                </div>
                <div class="border-l-4 border-gray-400 bg-gray-50 p-3">
                    <p class="text-xs font-bold text-gray-900">Intervention humaine significative — Art. 22 RGPD</p>
                    <p class="text-xs text-gray-700 mt-1">{{ $content['zones_grises']['human_washing'] }}</p>
                </div>
                <div class="border-l-4 border-gray-400 bg-gray-50 p-3">
                    <p class="text-xs font-bold text-gray-900">Data Privacy Framework et risque Schrems III</p>
                    <p class="text-xs text-gray-700 mt-1">{{ $content['zones_grises']['dpf'] }}</p>
                </div>
            </div>

            {{-- Disclaimer --}}
            <div class="bg-white shadow sm:rounded-lg p-6 space-y-3">
                <h3 class="font-semibold text-gray-900">Avertissement légal et limites de l'audit</h3>
                @foreach (['exclusion_responsabilite', 'peremption_normative', 'recommandation_assistance'] as $key)
                    <div>
                        <h4 class="font-semibold text-gray-800 text-sm">{{ $content['disclaimer'][$key]['titre'] }}</h4>
                        @foreach (preg_split("/\n\n/", $content['disclaimer'][$key]['contenu']) as $paragraph)
                            <p class="text-xs text-gray-700 mt-1">{{ $paragraph }}</p>
                        @endforeach
                    </div>
                @endforeach
            </div>

            <div class="text-right">
                <a href="{{ route('reports.index') }}" class="text-sm text-gray-600 hover:text-gray-800 underline">
                    Retour à l'historique
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
