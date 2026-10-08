<?php

namespace App\Filament\Resources\Pages;

use App\Filament\Resources\SiteSettingsResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSiteSettings extends ListRecords
{
    protected static string $resource = SiteSettingsResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
