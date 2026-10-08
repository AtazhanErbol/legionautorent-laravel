<?php

namespace App\Filament\Resources\Pages;

use App\Filament\Resources\FAQResource;
use App\Filament\Resources\Pages\CmsCreateRecord as CreateRecord;

class CreateFAQ extends CreateRecord
{
    protected static string $resource = FAQResource::class;

    public function getHeading(): string
    {
        return 'Новый вопрос';
    }
}
