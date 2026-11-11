<?php

namespace App\Modules\Core\Application\Services;

use Core\Models\NumberingRule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class NumberingService
{
    public function preview(int $companyId, string $formKey): string
    {
        $rule = NumberingRule::query()->where('company_id', $companyId)->where('form_key', $formKey)->where('is_active', true)->first();
        if (! $rule) return 'Nomor dibuat saat dikirim';
        $roman = ['', 'I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII'][now()->month];
        return $this->render($rule->format, (int) $rule->next_number, $roman);
    }

    public function next(int $companyId, string $formKey): string
    {
        return DB::connection('core')->transaction(function () use ($companyId, $formKey): string {
            $rule = NumberingRule::query()->where('company_id', $companyId)->where('form_key', $formKey)->where('is_active', true)->lockForUpdate()->first();
            if (! $rule) return 'HRIS-'.Str::upper(str_replace('_', '-', $formKey)).'-'.now()->format('Ymd').'-'.Str::upper(Str::random(6));
            $periodKey = match ($rule->counter_scope) {
                'daily' => now()->format('Ymd'),
                'monthly' => now()->format('Ym'),
                'yearly' => now()->format('Y'),
                default => 'global',
            };
            if ($rule->period_key !== $periodKey) {
                $rule->period_key = $periodKey;
                $rule->next_number = 1;
            }
            $number = (int) $rule->next_number;
            $rule->next_number = $number + 1;
            $rule->save();
            $roman = ['', 'I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII'][now()->month];
            return $this->render($rule->format, $number, $roman);
        });
    }

    private function render(string $format, int $number, string $roman): string
    {
        $format = preg_replace_callback('/\[C+\]/', fn ($match) => str_pad((string) $number, strlen($match[0]) - 2, '0', STR_PAD_LEFT), $format);
        return strtr($format, ['[no_urut]' => str_pad((string) $number, 4, '0', STR_PAD_LEFT), '[tahun]' => now()->format('Y'), '[bulan]' => now()->format('m'), '[bulan_romawi]' => $roman, '[hari]' => now()->format('d')]);
    }
}
