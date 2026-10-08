<?php

namespace App\Filament\RelationManagers;

use App\Filament\CmsFields;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;

abstract class CmsRelationManager extends RelationManager
{
    public function callMountedAction(array $arguments = []): mixed
    {
        try {
            return parent::callMountedAction($arguments);
        } catch (ValidationException $exception) {
            $prefix = ($this->getMountedActionSchema()?->getStatePath() ?? '').'.';
            $messages = [];
            if (collect(array_keys($exception->errors()))->every(fn (string $field): bool => str_starts_with($field, $prefix))) {
                throw $exception;
            }
            foreach ($exception->errors() as $field => $errors) {
                $messages[str_starts_with($field, $prefix) ? $field : $prefix.$field] = $errors;
            }
            throw ValidationException::withMessages($messages);
        }
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components(CmsFields::fields(static::$relatedResource::getModel(), static::$relationship === 'translations' ? ['content_type_id', 'object_id'] : ['car_id']))->columns(2);
    }

    public function table(Table $table): Table
    {
        $resource = static::$relatedResource;

        return $resource::table($table)->headerActions([CreateAction::make()->mutateFormDataUsing(function (array $data): array {
            if (static::$relationship === 'translations') {
                $data['content_type_id'] = $this->getOwnerRecord()->contentTypeId();
            }

            return $data;
        })])->recordActions([EditAction::make(), DeleteAction::make()])->toolbarActions([]);
    }

    public function isReadOnly(): bool
    {
        return ! static::$relatedResource::canEdit($this->getOwnerRecord());
    }

    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        return static::$relatedResource::canViewAny();
    }

    protected function canCreate(): bool
    {
        return static::$relatedResource::canCreate();
    }

    protected function canEdit(Model $record): bool
    {
        return static::$relatedResource::canEdit($record);
    }

    protected function canDelete(Model $record): bool
    {
        return static::$relatedResource::canDelete($record);
    }

    protected function canReorder(): bool
    {
        return static::$relatedResource::canReorder();
    }
}
