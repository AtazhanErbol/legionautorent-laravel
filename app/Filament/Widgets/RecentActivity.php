<?php

namespace App\Filament\Widgets;

use App\Models\LogEntry;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class RecentActivity extends TableWidget
{
    protected static ?string $heading = 'Мои последние изменения';

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table->query(LogEntry::where('user_id', auth()->id())->latest('action_time'))->columns([TextColumn::make('action_time')->label('Дата')->dateTime('d.m.Y H:i')->timezone(config('legion.display_timezone')), TextColumn::make('object_repr')->label('Что изменено'), TextColumn::make('action_flag')->label('Действие')->formatStateUsing(fn ($state) => [1 => 'Создано', 2 => 'Изменено', 3 => 'Удалено'][$state] ?? '')])->paginated([5, 10]);
    }
}
