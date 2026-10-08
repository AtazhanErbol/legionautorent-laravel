<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\CarResource;
use App\Filament\Resources\PageResource;
use App\Filament\Resources\SiteSettingsResource;
use Filament\Widgets\Widget;

class QuickStart extends Widget
{
    protected string $view = 'filament.widgets.quick-start';

    protected static ?int $sort = -2;

    protected int|string|array $columnSpan = 'full';

    public static function canView(): bool
    {
        return CarResource::canViewAny() || PageResource::canViewAny() || SiteSettingsResource::canViewAny();
    }

    protected function getViewData(): array
    {
        $links = [];
        if (CarResource::canCreate()) {
            $links[] = ['href' => CarResource::getUrl('create'), 'title' => 'Добавить автомобиль', 'text' => 'Название, фото и скидки — в одной карточке.'];
        }
        if (CarResource::canViewAny()) {
            $links[] = ['href' => CarResource::getUrl(), 'title' => 'Изменить автопарк', 'text' => 'Найти машину по названию, городу или классу.'];
        }
        if (PageResource::canViewAny()) {
            $links[] = ['href' => PageResource::getUrl(), 'title' => 'Редактировать страницы', 'text' => 'Контакты, политика конфиденциальности и условия.'];
        }
        if (SiteSettingsResource::canViewAny()) {
            $links[] = ['href' => SiteSettingsResource::getUrl(), 'title' => 'Настроить сайт', 'text' => 'Телефоны, логотип, анимация и партнёрство.'];
        }

        return ['links' => $links];
    }
}
