<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Rapport #{{ $report->id }}
        </h2>
    </x-slot>

    @php
        $org = $report->snapshot['organization'];
        $usages = $report->snapshot['usages'] ?? [];
        $summary = $report->snapshot['summary'] ?? [];
        $niveauLabels = [
            'INACCEPTABLE' => 'Risque inacceptable',
            'HAUT_RISQUE' => 'Haut risque',
            'RISQUE_LIMITE' => 'Risque limité',
            'RISQUE_MINIMAL' => 'Risque minimal',
            'NON_EVALUE' => 'Non évalué',
        ];
        $niveauColors = [
            'INACCEPTABLE' => 'bg-red-600',
            'HAUT_RISQUE' => 'bg-orange-600',
            'RISQUE_LIMITE' => 'bg-yellow-600',
            'RISQUE_MINIMAL' => 'bg-green-600',
            'NON_EVALUE' => 'bg-gray-500',
        ];
    @endphp

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="bg-white shadow sm:rounded-lg p-6 flex justify-between items-start">
                <div>
                    <p class="text-sm text-gray-700">{{ $org['name'] }}</p>
                    <p class="text-xs text-gray-500 mt-1">
                        Généré le {{ $report->created_at->format('d/m/Y à H:i') }}
                    </p>
                </div>
                <div>
                    @if ($report->isPaid())
                        <span class="inline-block px-3 py-1 rounded-full text-xs bg-green-100 text-green-800 font-semibold">
                            Payé
                        </span>
                        <a href="{{ route('reports.download', $report) }}"
                           class="ml-3 inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 transition">
                            Télécharger PDF
                        </a>
                    @else
                        <span class="inline-block px-3 py-1 rounded-full text-xs bg-amber-100 text-amber-800 font-semibold">
                            En attente de paiement
                        </span>
                    @endif
                </div>
            </div>

            <div class="bg-white shadow sm:rounded-lg p-6">
                <h3 class="font-semibold text-gray-800 mb-3">Synthèse</h3>
                <div class="grid grid-cols-5 gap-3">
                    @foreach (['INACCEPTABLE', 'HAUT_RISQUE', 'RISQUE_LIMITE', 'RISQUE_MINIMAL', 'NON_EVALUE'] as $niveau)
                        <div class="border border-gray-200 rounded-md p-3 text-center">
                            <div class="text-2xl font-bold text-gray-900">{{ $summary[$niveau] ?? 0 }}</div>
                            <div class="text-xs text-gray-500 uppercase mt-1">{{ $niveauLabels[$niveau] }}</div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="bg-white shadow sm:rounded-lg p-6 space-y-4">
                <h3 class="font-semibold text-gray-800">Usages déclarés</h3>
                @forelse ($usages as $usage)
                    <div class="border border-gray-200 rounded-md p-4">
                        <div class="flex items-center justify-between mb-2">
                            <h4 class="font-medium text-gray-900">{{ $usage['name'] }}</h4>
                            @if ($usage['assessment'])
                                @php $niveau = $usage['assessment']['niveau']; @endphp
                                <span class="inline-block px-2 py-1 rounded text-xs text-white {{ $niveauColors[$niveau] ?? 'bg-gray-500' }}">
                                    {{ $niveauLabels[$niveau] ?? $niveau }}
                                </span>
                            @endif
                        </div>
                        <p class="text-xs text-gray-500">Type : {{ $usage['type'] }} · Domaine : {{ $usage['domain'] }}</p>
                        @if ($usage['assessment'])
                            <p class="text-sm text-gray-700 mt-2">{{ $usage['assessment']['raison'] }}</p>
                        @else
                            <p class="text-sm text-gray-500 mt-2 italic">Non évalué.</p>
                        @endif
                    </div>
                @empty
                    <p class="text-sm text-gray-500">Aucun usage déclaré.</p>
                @endforelse
            </div>

            <div class="text-right">
                <a href="{{ route('reports.index') }}" class="text-sm text-gray-600 hover:text-gray-800 underline">
                    Retour à l'historique
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
