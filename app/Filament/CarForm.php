<?php

namespace App\Filament;

use App\Models\Car;
use App\Models\CarDiscount;
use App\Models\CarImage;
use App\Models\CarPrice;
use App\Models\CarSpecification;
use App\Services\CmsValidation;
use Closure;
use Filament\Forms\Components\Repeater;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Illuminate\Support\Str;

class CarForm
{
    public static function schema(): array
    {
        $main = CmsFields::inputs(Car::class, [], ['name', 'brand_id', 'model_name', 'category_id', 'cities', 'active', 'accepts_requests', 'featured', 'description']);
        foreach ($main as $field) {
            if ($field->getName() === 'cities') {
                $field->required()->helperText('Выберите хотя бы один город, в котором можно арендовать автомобиль.');
            }
            if ($field->getName() === 'name') {
                $field->live(onBlur: true)->afterStateUpdated(function (?string $state, string $operation, Get $get, Set $set): void {
                    if ($operation === 'create' && ! $get('slug')) {
                        $set('slug', Str::slug($state ?? ''));
                        $set('seo_title', $state);
                        $set('seo_h1', 'Аренда '.($state ?? ''));
                    }
                });
            }
        }

        $photos = self::related('images', CarImage::class, ['car_id', 'legacy_url', 'sort_order'])
            ->label('Галерея автомобиля')->addActionLabel('Добавить фотографию')
            ->orderColumn('sort_order')->reorderable()->reorderableWithButtons()
            ->grid(['default' => 1, 'xl' => 2])->collapsible()
            ->itemLabel(fn (array $state): string => ($state['is_main'] ?? false) ? 'Главная фотография' : (($state['caption'] ?? '') ?: 'Фотография'))
            ->helperText('Загрузите фото и выберите одно главное. Остальные можно перетаскивать. Все изменения сохраняются общей кнопкой «Сохранить».')
            ->rules([fn (): Closure => function (string $attribute, mixed $value, Closure $fail): void {
                if (collect(is_array($value) ? $value : [])->where('is_main', true)->count() > 1) {
                    $fail('Выберите только одно главное фото: выключите отметку у предыдущего.');
                }
            }]);

        $prices = self::related('prices', CarPrice::class, ['car_id'])
            ->label('Тарифы по сроку аренды')->addActionLabel('Добавить тариф')
            ->itemLabel(fn (array $state): string => ($state['label'] ?? '') ?: 'Тариф')
            ->helperText('Необязательно. Без дополнительных тарифов используется основная цена в сутки. Пустое «До дней» означает любой больший срок.')
            ->rules([self::rangeRule()]);
        $discounts = self::related('discounts', CarDiscount::class, ['car_id'])
            ->label('Скидки по сроку аренды')->addActionLabel('Добавить скидку')
            ->itemLabel(fn (array $state): string => ($state['label'] ?? '') ?: 'Скидка')
            ->helperText('Например: от 7 до 14 дней — 10%. Диапазоны не должны пересекаться; соседние могут иметь общую границу, как на исходном сайте. Дополнительные тарифы и скидки выводятся отдельно.')
            ->rules([self::rangeRule()]);

        return [Tabs::make('Карточка автомобиля')->persistTabInQueryString('tab')->columnSpanFull()->tabs([
            Tab::make('Автомобиль')->schema([Section::make('Основные данные')->description('Название, города, публикация и описание для посетителей.')->schema($main)->columns(['default' => 1, 'lg' => 2])]),
            Tab::make('Фотографии')->schema([$photos]),
            Tab::make('Цены и скидки')->schema([
                Section::make('Основная цена')->schema(CmsFields::inputs(Car::class, [], ['base_price', 'deposit', 'mileage_limit']))->columns(['default' => 1, 'lg' => 3]),
                $prices, $discounts,
            ]),
            Tab::make('Характеристики')->schema([
                Section::make('Параметры автомобиля')->schema(CmsFields::inputs(Car::class, [], ['year', 'engine', 'transmission', 'drive', 'seats', 'doors', 'color', 'fuel', 'features']))->columns(['default' => 1, 'lg' => 2]),
                self::related('extra_specs', CarSpecification::class, ['car_id', 'sort_order'])->label('Дополнительные характеристики')->orderColumn('sort_order')->reorderable()->addActionLabel('Добавить характеристику')->itemLabel(fn (array $state): string => ($state['label'] ?? '') ?: 'Характеристика'),
            ]),
            Tab::make('SEO и адрес')->schema([
                Section::make('Поисковая выдача')->description('Существующие адреса и SEO-тексты сохраняйте. Изменять можно осознанно, после проверки.')->schema(CmsFields::inputs(Car::class, [], ['seo_title', 'seo_description', 'seo_h1', 'canonical_url', 'robots']))->columns(['default' => 1, 'lg' => 2]),
                Section::make('Адрес страницы')->schema(CmsFields::inputs(Car::class, [], ['slug', 'legacy_path']))->columns(['default' => 1, 'lg' => 2]),
                Section::make('Картинка и текст при отправке ссылки')->schema(CmsFields::inputs(Car::class, [], ['og_title', 'og_description', 'og_image']))->columns(['default' => 1, 'lg' => 2])->collapsible()->collapsed(),
                Section::make('Исходные технические данные')->schema(CmsFields::inputs(Car::class, [], ['legacy_id', 'legacy_meta', 'sort_order']))->collapsible()->collapsed(),
            ]),
        ])];
    }

    private static function related(string $relationship, string $model, array $except): Repeater
    {
        $fields = CmsFields::inputs($model, $except);
        foreach ($fields as $field) {
            if (in_array($field->getName(), ['min_days', 'daily_price', 'percent'])) {
                $field->minValue(1);
            }
            if ($field->getName() === 'percent') {
                $field->maxValue(99);
            }
            if ($field->getName() === 'max_days') {
                $field->minValue(1);
            }
        }

        return Repeater::make($relationship)->relationship($relationship)->schema($fields)
            ->columns(['default' => 1, 'lg' => 2])->defaultItems(0)->columnSpanFull()
            ->addable(fn (): bool => auth()->user()?->hasCmsPermission('add', class_basename($model)) ?? false)
            ->deletable(fn (): bool => auth()->user()?->hasCmsPermission('delete', class_basename($model)) ?? false)
            ->disabled(fn (): bool => ! (auth()->user()?->hasCmsPermission('change', class_basename($model)) ?? false))
            ->deleteAction(fn ($action) => $action->requiresConfirmation())
            ->saveRelationshipsUsing(fn (Repeater $component) => self::saveRelated($component));
    }

    private static function rangeRule(): Closure
    {
        return fn (): Closure => function (string $attribute, mixed $value, Closure $fail): void {
            $rows = array_values(is_array($value) ? $value : []);
            foreach ($rows as $index => $row) {
                $from = (int) ($row['min_days'] ?? 0);
                $to = filled($row['max_days'] ?? null) ? (int) $row['max_days'] : PHP_INT_MAX;
                if ($from < 1 || $to < $from) {
                    $fail('Проверьте диапазон №'.($index + 1).': от 1 дня, окончание не раньше начала.');

                    return;
                }
                foreach (array_slice($rows, $index + 1) as $other) {
                    $otherFrom = (int) ($other['min_days'] ?? 0);
                    $otherTo = filled($other['max_days'] ?? null) ? (int) $other['max_days'] : PHP_INT_MAX;
                    if (CmsValidation::rangesOverlap($from, $to, $otherFrom, $otherTo)) {
                        $fail('Диапазоны пересекаются. Исправьте сроки до сохранения.');

                        return;
                    }
                }
            }
        };
    }

    private static function saveRelated(Repeater $component): void
    {
        $car = $component->getRecord();
        $relationship = $component->getRelationshipName();
        if ($relationship === 'images') {
            $selectedKey = collect($component->getRawState())->filter(fn (array $item): bool => (bool) ($item['is_main'] ?? false))->keys()->first();
            $selectedId = is_string($selectedKey) && str_starts_with($selectedKey, 'record-') ? (int) substr($selectedKey, 7) : 0;
            $car->images()->where('is_main', true)->where('id', '!=', $selectedId)->update(['is_main' => false]);
        }
        request()->attributes->set('legion.validated_car_relation', ['car_id' => $car->id, 'relationship' => $relationship]);
        try {
            $component->saveToRelationship();
        } finally {
            request()->attributes->remove('legion.validated_car_relation');
        }
    }
}
