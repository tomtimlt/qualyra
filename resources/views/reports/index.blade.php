<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Rapports de conformité
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @php
                $statusMessages = [
                    'report-paid' => ['type' => 'green', 'msg' => 'Paiement confirmé. Votre rapport est prêt.'],
                    'report-fake-paid' => ['type' => 'green', 'msg' => 'Rapport généré (mode dev — paiement court-circuité).'],
                    'payment-cancelled' => ['type' => 'yellow', 'msg' => 'Paiement annulé. Le rapport est en attente.'],
                    'payment-not-confirmed' => ['type' => 'yellow', 'msg' => 'Le paiement n\'a pas été confirmé par Stripe.'],
                    'payment-verification-failed' => ['type' => 'yellow', 'msg' => 'Vérification du paiement impossible. Réessayez ou contactez le support.'],
                    'reports-need-usages' => ['type' => 'yellow', 'msg' => 'Déclarez au moins un usage IA avant de générer un rapport.'],
                ];
                $status = session('status');
                $banner = $statusMessages[$status] ?? null;
            @endphp

            @if ($banner)
                <div class="rounded-md bg-{{ $banner['type'] }}-50 border border-{{ $banner['type'] }}-200 p-4 text-sm text-{{ $banner['type'] }}-800">
                    {{ $banner['msg'] }}
                </div>
            @endif

            <div class="bg-white shadow sm:rounded-lg p-6 flex items-center justify-between">
                <div>
                    <h3 class="font-semibold text-gray-800">Nouveau rapport</h3>
                    <p class="text-sm text-gray-600 mt-1">
                        Génère un rapport PDF figé de tous vos usages IA, avec leur niveau de risque AI Act et les alertes RGPD.
                    </p>
                </div>
                <form method="POST" action="{{ route('checkout.create') }}">
                    @csrf
                    <x-primary-button>Générer un rapport</x-primary-button>
                </form>
            </div>

            <div class="bg-white shadow sm:rounded-lg">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="font-semibold text-gray-800">Historique</h3>
                </div>
                @if ($reports->isEmpty())
                    <div class="p-6 text-sm text-gray-500">
                        Aucun rapport généré pour l'instant.
                    </div>
                @else
                    <ul class="divide-y divide-gray-200">
                        @foreach ($reports as $report)
                            <li class="p-6 flex items-center justify-between">
                                <div>
                                    <a href="{{ route('reports.show', $report) }}"
                                       class="text-sm font-medium text-indigo-600 hover:text-indigo-800">
                                        Rapport #{{ $report->id }}
                                    </a>
                                    <p class="text-xs text-gray-500 mt-1">
                                        Généré le {{ $report->created_at->format('d/m/Y à H:i') }}
                                        @if ($report->isPaid())
                                            · <span class="text-green-700">Payé le {{ $report->paid_at->format('d/m/Y') }}</span>
                                        @else
                                            · <span class="text-amber-700">En attente de paiement</span>
                                        @endif
                                    </p>
                                </div>
                                <div class="flex gap-2">
                                    @if ($report->isPaid())
                                        <a href="{{ route('reports.download', $report) }}"
                                           class="inline-flex items-center px-3 py-1.5 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 transition">
                                            Télécharger PDF
                                        </a>
                                    @endif
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
