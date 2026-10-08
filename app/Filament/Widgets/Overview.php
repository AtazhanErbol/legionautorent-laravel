<?php

namespace App\Filament\Widgets;

use App\Models\BookingRequest;
use App\Models\Car;
use App\Models\City;
use App\Models\Page;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class Overview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $u = auth()->user();
        $stats = [];
        if ($u->hasCmsPermission('view', 'BookingRequest')) {
            $stats[] = Stat::make('Новые заявки', BookingRequest::where('status', 'NEW')->count())->url('/'.config('legion.admin_path').'/leads')->color('warning');
        }if ($u->hasCmsPermission('view', 'Car')) {
            $stats[] = Stat::make('Автомобили', Car::public()->count())->url('/'.config('legion.admin_path').'/cars');
        }if ($u->hasCmsPermission('view', 'City')) {
            $stats[] = Stat::make('Города', City::where('active', true)->count());
        }if ($u->hasCmsPermission('view', 'Page')) {
            $stats[] = Stat::make('Страницы', Page::where('active', true)->count());
        }

        return $stats;
    }
}
