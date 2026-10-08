<?php

namespace App\Filament\Resources\Pages;

use App\Filament\Resources\PageResource;
use App\Filament\Resources\Pages\CmsEditRecord as EditRecord;

class EditPage extends EditRecord
{
    protected static string $resource = PageResource::class;

    public function getHeading(): string
    {
        return 'Редактирование страницы';
    }
}
