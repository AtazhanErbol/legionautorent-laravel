<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\BookingRequestResource;
use App\Models\BookingRequest;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class RecentLeads extends TableWidget
{
    protected static ?string $heading = 'Последние заявки';

    protected int|string|array $columnSpan = 'full';

    public static function canView(): bool
    {
        return auth()->user()?->hasCmsPermission('view', 'BookingRequest') ?? false;
    }

    public function table(Table $table): Table
    {
        return $table->query(BookingRequest::query()->with(['city', 'car'])->latest('id'))->columns([TextColumn::make('created_at')->label('Получена')->dateTime('d.m.Y H:i')->timezone(config('legion.display_timezone')), TextColumn::make('name')->label('Клиент'), TextColumn::make('phone')->label('Телефон'), TextColumn::make('city.name')->label('Город'), TextColumn::make('car.name')->label('Автомобиль'), TextColumn::make('status')->label('Статус')->badge()])->recordUrl(fn ($record) => BookingRequestResource::getUrl('edit', ['record' => $record]))->paginated([5, 10]);
    }
}
