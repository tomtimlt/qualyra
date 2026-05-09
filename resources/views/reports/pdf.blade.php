<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Rapport AI Act — {{ $report->snapshot['organization']['name'] ?? 'Rapport' }}</title>
    <style>
        @page { margin: 1.5cm; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #1f2937; line-height: 1.5; }
        h1 { font-size: 22px; margin: 0 0 4px; color: #111827; }
        h2 { font-size: 14px; margin: 24px 0 8px; padding-bottom: 4px; border-bottom: 1px solid #e5e7eb; color: #111827; }
        h3 { font-size: 12px; margin: 16px 0 4px; color: #1f2937; }
        .meta { color: #6b7280; font-size: 10px; margin-bottom: 16px; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        th, td { text-align: left; padding: 6px 8px; border-bottom: 1px solid #e5e7eb; vertical-align: top; }
        th { background: #f9fafb; font-weight: 600; font-size: 10px; text-transform: uppercase; color: #4b5563; }
        .badge { display: inline-block; padding: 2px 8px; border-radius: 999px; font-size: 9px; font-weight: 700; text-transform: uppercase; color: white; }
        .badge-INACCEPTABLE { background: #b91c1c; }
        .badge-HAUT_RISQUE { background: #c2410c; }
        .badge-RISQUE_LIMITE { background: #b45309; }
        .badge-RISQUE_MINIMAL { background: #15803d; }
        .badge-NON_EVALUE { background: #6b7280; }
        .summary-grid { width: 100%; margin-top: 8px; }
        .summary-grid td { width: 20%; text-align: center; border: 1px solid #e5e7eb; padding: 12px 4px; }
        .summary-grid .count { font-size: 22px; font-weight: 700; color: #111827; display: block; }
        .summary-grid .label { font-size: 9px; color: #6b7280; text-transform: uppercase; }
        .alert { background: #fef3c7; border-left: 3px solid #d97706; padding: 6px 10px; margin: 4px 0; font-size: 10px; }
        .footer { margin-top: 30px; font-size: 9px; color: #9ca3af; border-top: 1px solid #e5e7eb; padding-top: 8px; }
    </style>
</head>
<body>

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
@endphp

<h1>Rapport de conformité AI Act</h1>
<div class="meta">
    {{ $org['name'] }}
    @if (! empty($org['siret'])) · SIRET {{ $org['siret'] }} @endif
    @if (! empty($org['size'])) · {{ $org['size'] }} salariés @endif
    <br>
    Généré le {{ \Carbon\Carbon::parse($report->snapshot['generated_at'])->format('d/m/Y à H:i') }}
    · ID rapport #{{ $report->id }}
</div>

<h2>Synthèse</h2>
<table class="summary-grid">
    <tr>
        @foreach (['INACCEPTABLE', 'HAUT_RISQUE', 'RISQUE_LIMITE', 'RISQUE_MINIMAL', 'NON_EVALUE'] as $niveau)
            <td>
                <span class="count">{{ $summary[$niveau] ?? 0 }}</span>
                <span class="label">{{ $niveauLabels[$niveau] }}</span>
            </td>
        @endforeach
    </tr>
</table>

<h2>Détail des usages déclarés</h2>

@forelse ($usages as $usage)
    <h3>{{ $usage['name'] }}</h3>
    <table>
        <tr>
            <th width="20%">Type</th>
            <td>{{ $usage['type'] }}</td>
            <th width="20%">Domaine</th>
            <td>{{ $usage['domain'] }}</td>
        </tr>
        @if (! empty($usage['description']))
            <tr>
                <th>Description</th>
                <td colspan="3">{{ $usage['description'] }}</td>
            </tr>
        @endif
        @if ($usage['assessment'])
            @php $a = $usage['assessment']; @endphp
            <tr>
                <th>Niveau AI Act</th>
                <td colspan="3">
                    <span class="badge badge-{{ $a['niveau'] }}">{{ $niveauLabels[$a['niveau']] ?? $a['niveau'] }}</span>
                    {{ $a['article'] }}
                </td>
            </tr>
            <tr>
                <th>Justification</th>
                <td colspan="3">{{ $a['raison'] }}</td>
            </tr>
            @if (! empty($a['alertes']))
                <tr>
                    <th>Alertes</th>
                    <td colspan="3">
                        @foreach ($a['alertes'] as $alerte)
                            <div class="alert">
                                <strong>{{ $alerte['code'] }}</strong> — {{ $alerte['message'] }}
                            </div>
                        @endforeach
                    </td>
                </tr>
            @endif
        @else
            <tr>
                <th>Niveau AI Act</th>
                <td colspan="3"><span class="badge badge-NON_EVALUE">Non évalué</span></td>
            </tr>
        @endif
    </table>
@empty
    <p>Aucun usage déclaré.</p>
@endforelse

<div class="footer">
    Ce rapport est généré automatiquement à partir des déclarations effectuées dans l'outil AI Assistant.
    Il constitue une aide à la conformité et ne se substitue pas à une analyse juridique formelle.
    Règlement de référence : UE 2024/1689 (AI Act).
</div>

</body>
</html>
