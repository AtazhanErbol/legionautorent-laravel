<?php

namespace App\Filament\Resources\Pages;

use App\Filament\Resources\CarResource;
use App\Filament\Resources\Pages\CmsCreateRecord as CreateRecord;

class CreateCar extends CreateRecord
{
    protected static string $resource = CarResource::class;

    public function getHeading(): string
    {
        return 'Новый автомобиль';
    }

    public function getSubheading(): ?string
    {
        return 'Заполните основные данные, добавьте фото и цены во вкладках. Кнопка «Создать» сохраняет всю карточку.';
    }

    protected function getRedirectUrl(): string
    {
        return CarResource::getUrl('edit', ['record' => $this->getRecord()]);
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Автомобиль, фотографии и цены сохранены';
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['seo_title'] = $data['seo_title'] ?: $data['name'];
        $data['seo_h1'] = $data['seo_h1'] ?: 'Аренда '.$data['name'];
        $data['seo_description'] = $data['seo_description'] ?: 'Аренда '.$data['name'].' без водителя. Цена от '.number_format((float) $data['base_price'], 0, '.', ' ').' ₸ в сутки. LEGIONAUTORENT.';

        return $data;
    }
}
