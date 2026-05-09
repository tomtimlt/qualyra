<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $aiUsage->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">
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
        </div>
    </div>
</x-app-layout>
