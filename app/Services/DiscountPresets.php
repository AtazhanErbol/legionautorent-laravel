<?php

namespace App\Services;

use App\Models\Car;

class DiscountPresets
{
    /** @return array<string, array{label: string, description: string, rows: array, uses: int}> */
    public static function all(): array
    {
        $sets = [];
        foreach (Car::query()->with('discounts')->whereHas('discounts')->orderBy('id')->get(['id']) as $car) {
            $rows = $car->discounts->map(fn ($discount): array => [
                'label' => $discount->label,
                'min_days' => (int) $discount->min_days,
                'max_days' => $discount->max_days === null ? null : (int) $discount->max_days,
                'percent' => (int) $discount->percent,
            ])->all();
            $key = hash('sha256', json_encode($rows, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR));
            if (isset($sets[$key])) {
                $sets[$key]['uses']++;

                continue;
            }
            $description = implode('; ', array_map(fn (array $row): string => ($row['max_days'] === null ? 'от '.$row['min_days'].' дней' : $row['min_days'].'–'.$row['max_days'].' дней').' — '.$row['percent'].'%', $rows));
            $sets[$key] = ['label' => '', 'description' => $description, 'rows' => $rows, 'uses' => 1];
        }
        uasort($sets, fn (array $a, array $b): int => $b['uses'] <=> $a['uses']);
        $number = 0;
        foreach ($sets as &$set) {
            $number++;
            $set['label'] = ($number === 1 && $set['uses'] > 1 ? 'Стандартные скидки' : 'Набор '.$number).' · '.$set['description'];
        }
        unset($set);

        return $sets;
    }

    public static function options(): array
    {
        return array_map(fn (array $set): string => $set['label'], self::all());
    }

    public static function rows(string $key): array
    {
        $set = self::all()[$key] ?? null;
        if ($set === null) {
            CmsValidation::fail('discount_preset', 'Этот набор больше недоступен. Выберите другой или заполните скидки вручную.');
        }

        return $set['rows'];
    }
}
