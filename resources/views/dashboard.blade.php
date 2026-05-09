<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tableau de bord') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('status') === 'organization-created')
                <div class="rounded-md bg-green-50 p-4 border border-green-200 text-sm text-green-800">
                    Votre organisation a bien été créée. Vous pouvez maintenant déclarer vos usages d'IA.
                </div>
            @endif

            {{-- Pas d'organisation : on invite l'utilisateur à la créer avant tout autre action --}}
            @if ($organization === null)
                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900">Bienvenue {{ Auth::user()->name }}</h3>
                    <p class="mt-2 text-sm text-gray-600">
                        Pour commencer l'audit de conformité AI Act + RGPD, créez d'abord la fiche de votre PME.
                    </p>
                    <div class="mt-4">
                        <a href="{{ route('organization.create') }}"
                           class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition">
                            Créer mon organisation
                        </a>
                    </div>
                </div>
            @else
                {{-- Vue principale : récap organisation + liste des usages déclarés --}}
                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">{{ $organization->name }}</h3>
                            <p class="text-sm text-gray-500">
                                Taille : {{ $organization->size }}
                                @if ($organization->sector)
                                    · Secteur : {{ $organization->sector }}
                                @endif
                            </p>
                        </div>
                        <a href="{{ route('usages.create') }}"
                           class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition">
                            Déclarer un usage IA
                        </a>
                    </div>
                </div>

                <div class="bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-base font-semibold text-gray-900">Usages d'IA déclarés ({{ $aiUsages->count() }})</h3>
                    </div>

                    @if ($aiUsages->isEmpty())
                        <div class="p-6 text-sm text-gray-500">
                            Aucun usage d'IA déclaré pour l'instant. Cliquez sur « Déclarer un usage IA » pour commencer.
                        </div>
                    @else
                        <ul class="divide-y divide-gray-200">
                            @foreach ($aiUsages as $usage)
                                <li class="p-6 flex items-center justify-between">
                                    <div>
                                        <a href="{{ route('usages.show', $usage) }}"
                                           class="text-sm font-medium text-indigo-600 hover:text-indigo-800">
                                            {{ $usage->name }}
                                        </a>
                                        <p class="text-xs text-gray-500 mt-1">
                                            Type : {{ $usage->type }} · Domaine : {{ $usage->domain }}
                                        </p>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('usages.edit', $usage) }}"
                                           class="text-xs text-gray-600 hover:text-gray-800 underline">Modifier</a>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
