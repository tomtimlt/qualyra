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
    $rule = ['id' => 'R-H-02', 'applicable_from' => '2026-08-02'];

    expect(app(TemporalRuleEvaluator::class)->isApplicable($rule, Carbon::parse('2026-08-01')))
        ->toBeFalse();
});

it('accepte une règle pile à la date d\'entrée en vigueur', function () {
    $rule = ['id' => 'R-H-02', 'applicable_from' => '2026-08-02'];

    expect(app(TemporalRuleEvaluator::class)->isApplicable($rule, Carbon::parse('2026-08-02')))
        ->toBeTrue();
});

it('accepte une règle évaluée largement après sa date d\'entrée en vigueur', function () {
    $rule = ['id' => 'R-I-01', 'applicable_from' => '2025-02-02'];

    expect(app(TemporalRuleEvaluator::class)->isApplicable($rule, Carbon::parse('2030-01-01')))
        ->toBeTrue();
});

it('rejette R-H-08 (médical MDR/IVDR) avant 2027-08-02', function () {
    $rule = ['id' => 'R-H-08', 'applicable_from' => '2027-08-02'];

    expect(app(TemporalRuleEvaluator::class)->isApplicable($rule, Carbon::parse('2026-12-31')))
        ->toBeFalse();

    expect(app(TemporalRuleEvaluator::class)->isApplicable($rule, Carbon::parse('2027-08-02')))
        ->toBeTrue();
});
