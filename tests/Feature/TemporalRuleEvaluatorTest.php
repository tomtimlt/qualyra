<?php

declare(strict_types=1);

use App\Services\TemporalRuleEvaluator;
use Illuminate\Support\Carbon;

it('considère une règle sans applicable_from comme toujours applicable', function () {
    $rule = ['id' => 'DEFAULT', 'niveau' => 'RISQUE_MINIMAL'];

    expect(app(TemporalRuleEvaluator::class)->isApplicable($rule, Carbon::parse('2020-01-01')))
        ->toBeTrue();
});

it('rejette une règle haut risque évaluée avant sa date d\'entrée en vigueur', function () {
    $rule = ['id' => 'R-H-02', 'applicable_from' => '2027-12-02'];

    expect(app(TemporalRuleEvaluator::class)->isApplicable($rule, Carbon::parse('2027-12-01')))
        ->toBeFalse();
});

it('accepte une règle pile à la date d\'entrée en vigueur', function () {
    $rule = ['id' => 'R-H-02', 'applicable_from' => '2027-12-02'];

    expect(app(TemporalRuleEvaluator::class)->isApplicable($rule, Carbon::parse('2027-12-02')))
        ->toBeTrue();
});

it('accepte une règle évaluée largement après sa date d\'entrée en vigueur', function () {
    $rule = ['id' => 'R-I-01', 'applicable_from' => '2025-02-02'];

    expect(app(TemporalRuleEvaluator::class)->isApplicable($rule, Carbon::parse('2030-01-01')))
        ->toBeTrue();
});

it('rejette R-H-08 (médical MDR/IVDR) avant 2028-08-02', function () {
    $rule = ['id' => 'R-H-08', 'applicable_from' => '2028-08-02'];

    expect(app(TemporalRuleEvaluator::class)->isApplicable($rule, Carbon::parse('2028-08-01')))
        ->toBeFalse();

    expect(app(TemporalRuleEvaluator::class)->isApplicable($rule, Carbon::parse('2028-08-02')))
        ->toBeTrue();
});

it('aligne les dates des règles sur le calendrier AI Act post-Digital Omnibus', function () {
    $dates = collect(config('ai_act_rules'))
        ->filter(fn (array $rule) => isset($rule['applicable_from']))
        ->mapWithKeys(fn (array $rule) => [$rule['id'] => $rule['applicable_from']]);

    $dates->each(function (string $date, string $id) {
        $expected = match (true) {
            str_starts_with($id, 'R-I-') => '2025-02-02',  // Art. 5
            str_starts_with($id, 'R-L-') => '2026-08-02',  // Art. 50
            $id === 'R-H-08' => '2028-08-02',              // Annexe I
            str_starts_with($id, 'R-H-'), $id === 'AGGRAVATION-CTRL' => '2027-12-02', // Annexe III
            default => $date,
        };

        expect($date)->toBe($expected, "Date inattendue pour {$id}");
    });
});
