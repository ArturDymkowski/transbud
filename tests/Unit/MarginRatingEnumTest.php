<?php

use App\Enums\MarginRatingEnum;

test('null margin (unknown/zero revenue) is rated as warning', function () {
    expect(MarginRatingEnum::fromPercent(null))->toBe(MarginRatingEnum::WARNING);
});

test('a margin below the warning threshold is rated as poor', function () {
    expect(MarginRatingEnum::fromPercent(-5.0))->toBe(MarginRatingEnum::POOR);
});

test('a margin exactly at the warning threshold is not poor', function () {
    expect(MarginRatingEnum::fromPercent(0.0))->toBe(MarginRatingEnum::WARNING);
});

test('a margin between the thresholds is rated as warning', function () {
    expect(MarginRatingEnum::fromPercent(5.0))->toBe(MarginRatingEnum::WARNING);
});

test('a margin exactly at the good threshold is rated as good', function () {
    expect(MarginRatingEnum::fromPercent(10.0))->toBe(MarginRatingEnum::GOOD);
});

test('a margin above the good threshold is rated as good', function () {
    expect(MarginRatingEnum::fromPercent(25.0))->toBe(MarginRatingEnum::GOOD);
});

test('each rating maps to its own badge color', function () {
    expect(MarginRatingEnum::POOR->color())->toBe('#f04438')
        ->and(MarginRatingEnum::WARNING->color())->toBe('#f79009')
        ->and(MarginRatingEnum::GOOD->color())->toBe('#12b76a');
});
