<?php

namespace App\Services\Documents;

class DocumentMetadataValueNormalizer
{
    public static function numeric(mixed $value, string $type = 'numeric'): mixed
    {
        if ($value === null || trim((string) $value) === '') {
            return $value;
        }

        $value = trim((string) $value);
        $value = str_replace(["\u{00A0}", ' '], '', $value);

        $configuredThousandsSeparator = (string) config('formatting.thousands_separator', '.');
        $configuredDecimalSeparator = (string) config('formatting.decimal_separator', ',');

        if ($configuredThousandsSeparator !== '.') {
            $value = str_replace($configuredThousandsSeparator, '.', $value);
        }

        if ($configuredDecimalSeparator !== ',') {
            $value = str_replace($configuredDecimalSeparator, ',', $value);
        }

        if ($type === 'numeric') {
            if (preg_match('/^(-?)(\d{1,3}(?:\.\d{3})+)$/', $value, $matches) === 1) {
                return $matches[1].str_replace('.', '', $matches[2]);
            }

            return $value;
        }

        // Indonesian format uses dots for thousands and a comma for decimals.
        if (substr_count($value, ',') === 1) {
            [$integer, $fraction] = explode(',', $value, 2);
            $sign = str_starts_with($integer, '-') ? '-' : '';
            $integer = ltrim($integer, '+-');

            if (preg_match('/^(?:\d+|\d{1,3}(?:\.\d{3})+)$/', $integer) === 1 && preg_match('/^\d+$/', $fraction) === 1) {
                return $sign.str_replace('.', '', $integer).'.'.$fraction;
            }
        }

        // A dot-only value is either an Indonesian thousands grouping or a
        // canonical decimal value using the international separator.
        if (preg_match('/^-?\d{1,3}(?:\.\d{3})+$/', $value) === 1) {
            return str_replace('.', '', $value);
        }

        return self::isValidNumeric($value, $type) ? $value : (string) $value;
    }

    public static function isValidNumeric(mixed $value, string $type = 'numeric'): bool
    {
        $pattern = $type === 'numeric_decimal'
            ? '/^-?(?:\d+)(?:\.\d+)?$/'
            : '/^-?\d+$/';

        return preg_match($pattern, (string) $value) === 1;
    }

    public static function formatIndonesian(mixed $value): mixed
    {
        if ($value === null || trim((string) $value) === '') {
            return $value;
        }

        $canonical = self::numeric($value, 'numeric_decimal');

        if (! self::isValidNumeric($canonical, 'numeric_decimal')) {
            return $value;
        }

        [$integer, $fraction] = array_pad(explode('.', (string) $canonical, 2), 2, null);
        $sign = str_starts_with($integer, '-') ? '-' : '';
        $integer = ltrim($integer, '-');
        $thousandsSeparator = (string) config('formatting.thousands_separator', '.');
        $decimalSeparator = (string) config('formatting.decimal_separator', ',');
        $integer = preg_replace('/\B(?=(\d{3})+(?!\d))/', $thousandsSeparator, $integer) ?? $integer;

        return $sign.$integer.($fraction !== null ? $decimalSeparator.$fraction : '');
    }
}
