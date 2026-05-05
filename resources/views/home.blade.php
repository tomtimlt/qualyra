@extends('layouts.public')

@section('content')
<div class="relative isolate px-6 pt-14 lg:px-8">
    <div class="mx-auto max-w-2xl py-32 sm:py-48 lg:py-56">
        <div class="text-center">
            <h1 class="text-4xl font-bold tracking-tight text-gray-900 sm:text-6xl">
                Audit de conformité AI Act + RGPD
            </h1>
            <p class="mt-6 text-lg leading-8 text-gray-600">
                Vérifiez la conformité de vos usages d'intelligence artificielle 
                au regard du règlement européen AI Act et du RGPD. 
                Obtenez un rapport détaillé avec plan d'action personnalisé pour votre PME.
            </p>
            <div class="mt-10 flex items-center justify-center gap-x-6">
                <a href="{{ route('register') }}" class="rounded-md bg-blue-600 px-6 py-3 text-lg font-semibold text-white shadow-sm hover:bg-blue-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">
                    Commencer mon audit
                </a>
                <a href="#comment-ca-marche" class="text-lg font-semibold leading-6 text-gray-900">
                    Comment ça marche ? <span aria-hidden="true">→</span>
                </a>
            </div>
        </div>
    </div>
</div>

<section id="comment-ca-marche" class="bg-white py-16">
    <div class="mx-auto max-w-7xl px-6 lg:px-8">
        <div class="mx-auto max-w-2xl lg:text-center">
            <h2 class="text-base font-semibold leading-7 text-blue-600">Simple et rapide</h2>
            <p class="mt-2 text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl">
                Comment fonctionne l'audit ?
            </p>
            <p class="mt-6 text-lg leading-8 text-gray-600">
                En 3 étapes, obtenez votre rapport de conformité complet.
            </p>
        </div>
        <div class="mx-auto mt-16 max-w-2xl sm:mt-20 lg:mt-24 lg:max-w-none">
            <dl class="grid max-w-xl grid-cols-1 gap-x-8 gap-y-16 lg:max-w-none lg:grid-cols-3">
                <div class="flex flex-col">
                    <dt class="flex items-center gap-x-3 text-base font-semibold leading-7 text-gray-900">
                        <span class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-600 text-white font-bold">1</span>
                        Déclarez vos usages IA
                    </dt>
                    <dd class="mt-4 flex flex-auto flex-col text-base leading-7 text-gray-600">
                        <p class="flex-auto">Listez les outils et systèmes d'IA utilisés dans votre entreprise (ChatGPT, outils de scoring, génération de contenu, etc.).</p>
                    </dd>
                </div>
                <div class="flex flex-col">
                    <dt class="flex items-center gap-x-3 text-base font-semibold leading-7 text-gray-900">
                        <span class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-600 text-white font-bold">2</span>
                        Répondez au questionnaire
                    </dt>
                    <dd class="mt-4 flex flex-auto flex-col text-base leading-7 text-gray-600">
                        <p class="flex-auto">Pour chaque usage, répondez à des questions ciblées sur la finalité, les données traitées et le niveau d'automatisation.</p>
                    </dd>
                </div>
                <div class="flex flex-col">
                    <dt class="flex items-center gap-x-3 text-base font-semibold leading-7 text-gray-900">
                        <span class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-600 text-white font-bold">3</span>
                        Recevez votre rapport
                    </dt>
                    <dd class="mt-4 flex flex-auto flex-col text-base leading-7 text-gray-600">
                        <p class="flex-auto">Obtenez un rapport PDF détaillé avec le niveau de risque pour chaque usage et un plan d'action personnalisé.</p>
                    </dd>
                </div>
            </dl>
        </div>
    </div>
</section>

<section class="bg-gray-50 py-16">
    <div class="mx-auto max-w-7xl px-6 lg:px-8">
        <div class="mx-auto max-w-2xl text-center">
            <h2 class="text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl">
                Pourquoi se conformer ?
            </h2>
            <p class="mt-6 text-lg leading-8 text-gray-600">
                L'AI Act impose des obligations légales aux entreprises utilisant des systèmes d'IA.
            </p>
        </div>
        <div class="mt-16 grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-2">
            <div class="bg-white p-6 rounded-lg shadow-sm">
                <h3 class="text-xl font-semibold text-gray-900">Évitez les sanctions</h3>
                <p class="mt-4 text-gray-600">
                    Les non-conformités peuvent entraîner des amendes jusqu'à 35 millions d'euros ou 7% du chiffre d'affaires mondial.
                </p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow-sm">
                <h3 class="text-xl font-semibold text-gray-900">Rassurez vos clients</h3>
                <p class="mt-4 text-gray-600">
                    Démontrer votre conformité renforce la confiance de vos clients et partenaires commerciaux.
                </p>
            </div>
        </div>
    </div>
</section>

<div class="bg-blue-600 py-12">
    <div class="mx-auto max-w-7xl px-6 lg:px-8 text-center">
        <h2 class="text-2xl font-bold text-white mb-6">
            Prêt à commencer votre audit de conformité ?
        </h2>
        <a href="{{ route('register') }}" class="inline-block rounded-md bg-white px-8 py-3 text-lg font-semibold text-blue-600 shadow-sm hover:bg-gray-100">
            Commencer mon audit gratuit
        </a>
    </div>
</div>
@endsection
