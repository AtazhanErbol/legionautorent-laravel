<?php

namespace App\Filament\Resources\Pages;

use App\Filament\Resources\CarResource;
use App\Filament\Resources\Pages\CmsEditRecord as EditRecord;
use Filament\Actions\Action;

class EditCar extends EditRecord
{
    protected static string $resource = CarResource::class;

    public function getHeading(): string
    {
        return $this->getRecord()->name;
    }

    public function getSubheading(): ?string
    {
        return 'Фотографии, цены, скидки и SEO редактируются во вкладках. Общая кнопка «Сохранить» применяет все изменения.';
    }

    protected function getHeaderActions(): array
    {
        return [Action::make('saveAll')->label('Сохранить всё')->action(fn () => $this->save())->keyBindings(['mod+s']), Action::make('preview')->label('Посмотреть на сайте')->url(fn (): string => $this->getRecord()->getAbsoluteUrl())->openUrlInNewTab()];
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Автомобиль и все связанные данные сохранены';
    }
}
