<?php

namespace App\Filament\RelationManagers;

use App\Filament\CmsFields;
use App\Filament\Resources\TranslationResource;
use App\Models\ContentType;
use App\Models\Translation;
use Filament\Schemas\Schema;

class TranslationsRelationManager extends CmsRelationManager
{
    protected static string $relationship = 'translations';

    protected static ?string $relatedResource = TranslationResource::class;

    protected static ?string $title = 'Переводы KZ / EN';

    public function form(Schema $schema): Schema
    {
        $type = ContentType::find($this->getOwnerRecord()->contentTypeId())?->model ?? '';

        return $schema->components(CmsFields::inputs(Translation::class, ['content_type_id', 'object_id'], ['language', 'published', ...CmsFields::translationFields($type)]))->columns(['default' => 1, 'lg' => 2]);
    }
}
