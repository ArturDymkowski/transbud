<?php

function langDir(string $locale): string
{
    return dirname(__DIR__, 3)."/resources/lang/{$locale}";
}

function flattenTranslationKeys(array $translations, string $prefix = ''): array
{
    $keys = [];

    foreach ($translations as $key => $value) {
        $fullKey = $prefix === '' ? (string) $key : "{$prefix}.{$key}";

        if (is_array($value)) {
            $keys = array_merge($keys, flattenTranslationKeys($value, $fullKey));
        } else {
            $keys[] = $fullKey;
        }
    }

    return $keys;
}

test('pl and en language directories contain the same set of files', function () {
    $plFiles = collect(glob(langDir('pl').'/*.php'))->map(fn ($path) => basename($path))->sort()->values();
    $enFiles = collect(glob(langDir('en').'/*.php'))->map(fn ($path) => basename($path))->sort()->values();

    expect($enFiles->all())->toBe($plFiles->all());
});

test('pl and en translation files define the same keys', function (string $file) {
    $plKeys = flattenTranslationKeys(require langDir('pl')."/{$file}");
    $enKeys = flattenTranslationKeys(require langDir('en')."/{$file}");

    sort($plKeys);
    sort($enKeys);

    expect($enKeys)->toBe($plKeys, "Key mismatch between pl/{$file} and en/{$file}.");
})->with(function () {
    return collect(glob(langDir('pl').'/*.php'))->map(fn ($path) => basename($path))->all();
});
