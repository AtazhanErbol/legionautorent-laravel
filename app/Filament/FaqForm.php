<?php

namespace App\Filament;

use App\Models\FAQ;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;

class FaqForm
{
    public static function schema(): array
    {
        $links = CmsFields::inputs(FAQ::class, [], ['city_id', 'page_id', 'car_id']);
        $places = ['city_id' => 'city', 'page_id' => 'page', 'car_id' => 'car'];
        foreach ($links as $field) {
            $place = $places[$field->getName()];
            $field->visible(fn (Get $get): bool => $get('faq_placement') === $place)
                ->required(fn (Get $get): bool => $get('faq_placement') === $place)
                ->dehydratedWhenHidden()->dehydrateStateUsing(fn (mixed $state, Get $get): mixed => $get('faq_placement') === $place ? $state : null)
                ->helperText('Выберите, где посетители увидят этот вопрос и ответ.');
        }

        return [
            Section::make('Вопрос и ответ')->schema(CmsFields::inputs(FAQ::class, [], ['question', 'answer']))->columnSpanFull(),
            Section::make('Где показывать')->description('Общий вопрос виден на главных всех городов и в «Вопросах и ответах». Для частных условий выберите город, страницу или автомобиль.')->schema([
                Select::make('faq_placement')->label('Место показа')
                    ->options(['general' => 'Все города', 'city' => 'Один город', 'page' => 'Одна страница', 'car' => 'Один автомобиль'])
                    ->default(fn (?FAQ $record): string => $record?->car_id ? 'car' : ($record?->page_id ? 'page' : ($record?->city_id ? 'city' : 'general')))->required()->live()->dehydrated(false)
                    ->afterStateHydrated(function (Select $component, ?FAQ $record): void {
                        if (blank($component->getState())) {
                            $component->state($record?->car_id ? 'car' : ($record?->page_id ? 'page' : ($record?->city_id ? 'city' : 'general')));
                        }
                    })
                    ->afterStateUpdated(function (Set $set): void {
                        foreach (['city_id', 'page_id', 'car_id'] as $field) {
                            $set($field, null);
                        }
                    }),
                ...$links,
            ])->columns(['default' => 1, 'lg' => 2])->columnSpanFull(),
            Section::make('Публикация')->schema(CmsFields::inputs(FAQ::class, [], ['active', 'sort_order']))->columns(2)->columnSpanFull(),
        ];
    }
}
