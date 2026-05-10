<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Déclarer un usage d'IA
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow sm:rounded-lg p-6">
                <form method="POST" action="{{ route('usages.store') }}" class="space-y-6">
                    @csrf
                    @include('usages._form')

                    <div class="flex items-center justify-end gap-3">
                        <a href="{{ route('usages.index') }}"
                           class="text-sm text-gray-600 hover:text-gray-800 underline">Annuler</a>
                        <x-primary-button>Enregistrer</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
