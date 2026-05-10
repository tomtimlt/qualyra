<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Mes usages d'IA
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('status') === 'usage-created')
                <div class="rounded-md bg-green-50 p-4 border border-green-200 text-sm text-green-800">
                    Usage IA créé avec succès.
                </div>
            @elseif (session('status') === 'usage-updated')
                <div class="rounded-md bg-green-50 p-4 border border-green-200 text-sm text-green-800">
                    Usage IA mis à jour.
                </div>
            @elseif (session('status') === 'usage-deleted')
                <div class="rounded-md bg-yellow-50 p-4 border border-yellow-200 text-sm text-yellow-800">
                    Usage IA supprimé.
                </div>
            @endif

            <div class="flex justify-end">
                <a href="{{ route('usages.create') }}"
                   class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition">
                    Déclarer un usage IA
                </a>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg">
                @if ($aiUsages->isEmpty())
                    <div class="p-6 text-sm text-gray-500">
                        Aucun usage d'IA déclaré pour l'instant.
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
                                        @if ($usage->responses()->exists())
                                            · <span class="text-green-700">Questionnaire renseigné</span>
                                        @else
                                            · <span class="text-amber-700">Questionnaire à compléter</span>
                                        @endif
                                    </p>
                                </div>
                                <div class="flex items-center gap-3">
                                    <a href="{{ route('usages.edit', $usage) }}"
                                       class="text-xs text-gray-600 hover:text-gray-800 underline">Modifier</a>
                                    <form method="POST" action="{{ route('usages.destroy', $usage) }}"
                                          onsubmit="return confirm('Supprimer définitivement cet usage ?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="text-xs text-red-600 hover:text-red-800 underline">
                                            Supprimer
                                        </button>
                                    </form>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
