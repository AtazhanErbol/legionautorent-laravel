<?php

namespace App\Filament\Resources;

use App\Filament\CmsFields;
use App\Filament\RelationManagers\DiscountsRelationManager;
use App\Filament\RelationManagers\PhotosRelationManager;
use App\Filament\RelationManagers\PricesRelationManager;
use App\Filament\RelationManagers\SpecificationsRelationManager;
use App\Filament\RelationManagers\TranslationsRelationManager;
use App\Models\BookingRequest;
use App\Models\Car;
use App\Models\CarBrand;
use App\Models\CarCategory;
use App\Models\CarImage;
use App\Models\CarSpecification;
use App\Models\City;
use App\Models\ContentBlock;
use App\Models\FAQ;
use App\Models\Page;
use App\Models\SiteSettings;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

abstract class CmsResource extends Resource
{
    protected static bool $isDiscovered = false;

    protected static function allowed(string $action): bool
    {
        return auth()->user()?->hasCmsPermission($action, class_basename(static::getModel())) ?? false;
    }

    public static function canViewAny(): bool
    {
        return static::allowed('view') || static::allowed('change');
    }

    public static function canCreate(): bool
    {
        return static::allowed('add') && ! in_array(static::getModel(), [SiteSettings::class, BookingRequest::class]);
    }

    public static function canEdit(Model $record): bool
    {
        return static::allowed('change');
    }

    public static function canDelete(Model $record): bool
    {
        if (! static::allowed('delete') || in_array(static::getModel(), [SiteSettings::class, BookingRequest::class])) {
            return false;
        }

        return match (true) {
            $record instanceof Car => ! BookingRequest::where('car_id', $record->id)->exists(),
            $record instanceof City => ! BookingRequest::where('city_id', $record->id)->exists(),
            $record instanceof CarCategory => ! Car::where('category_id', $record->id)->exists(),
            $record instanceof CarBrand => ! Car::where('brand_id', $record->id)->exists(),
            default => true,
        };
    }

    public static function canDeleteAny(): bool
    {
        return false;
    }

    public static function canReorder(): bool
    {
        return static::allowed('change');
    }

    public static function getRelations(): array
    {
        $model = static::getModel();
        $managers = [];
        if ($model === Car::class) {
            $managers = [PhotosRelationManager::class, PricesRelationManager::class, DiscountsRelationManager::class, SpecificationsRelationManager::class];
        }
        if (in_array($model, [Car::class, City::class, Page::class, CarCategory::class, CarImage::class, ContentBlock::class, FAQ::class, SiteSettings::class, CarSpecification::class])) {
            $managers[] = TranslationsRelationManager::class;
        }

        return $managers;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components(CmsFields::fields(static::getModel()))->columns(2);
    }

    public static function table(Table $table): Table
    {
        $def = CmsFields::definition(static::getModel());
        $fields = array_column($def['fields'], 'column');
        $title = CmsFields::titleAttribute(static::getModel());
        $columns = [TextColumn::make($title)->label($def['name'])->searchable()->forceSearchCaseInsensitive()->sortable()->limit(75)];
        foreach (['base_price', 'phone', 'kind', 'section', 'language', 'status', 'path', 'sort_order'] as $f) {
            if (in_array($f, $fields) && $f !== $title) {
                $columns[] = TextColumn::make($f)->label(collect($def['fields'])->firstWhere('column', $f)['label'])->sortable()->searchable()->forceSearchCaseInsensitive()->badge(in_array($f, ['status', 'language', 'kind']));
            }
        }
        if (in_array('active', $fields)) {
            $columns[] = IconColumn::make('active')->label('Опубликовано')->boolean();
        }if (in_array('published', $fields)) {
            $columns[] = IconColumn::make('published')->label('Проверен')->boolean();
        }if (in_array('original', $fields)) {
            array_unshift($columns, ImageColumn::make('original')->label('Фото')->disk('media'));
        }
        $filters = [];
        if (in_array('active', $fields)) {
            $filters[] = TernaryFilter::make('active')->label('Опубликовано');
        }foreach ($def['fields'] as $f) {
            if ($f['choices']) {
                $filters[] = SelectFilter::make($f['column'])->label($f['label'])->options(collect($f['choices'])->mapWithKeys(fn ($c) => [$c[0] => $c[1]])->all());
            }
        }
        $actions = [EditAction::make(), DeleteAction::make()];
        $toolbar = [];
        if (static::getModel() === BookingRequest::class) {
            $toolbar[] = Action::make('csv')->label('Скачать CSV')->action(function ($livewire) {
                return response()->streamDownload(function () use ($livewire) {
                    $fp = fopen('php://output', 'w');
                    fwrite($fp, "\xEF\xBB\xBF");
                    $cols = ['id', 'created_at', 'name', 'phone', 'city_id', 'car_id', 'status', 'kind', 'start_date', 'end_date', 'comment', 'manager_note', 'source_page', 'utm_source', 'utm_medium', 'utm_campaign'];
                    fputcsv($fp, $cols);
                    foreach ($livewire->getFilteredTableQuery()->cursor() as $r) {
                        $row = [];
                        foreach ($cols as $c) {
                            $s = (string) $r->$c;
                            if (preg_match('/^[=+\-@\t\r]/', $s)) {
                                $s = "'".$s;
                            }$row[] = $s;
                        }fputcsv($fp, $row);
                    }fclose($fp);
                }, 'legion-leads.csv', ['Content-Type' => 'text/csv; charset=utf-8']);
            });
        }
        $table->columns($columns)->filters($filters)->recordActions($actions)->toolbarActions($toolbar)->paginated([25, 50, 100])->defaultSort(in_array('sort_order', $fields) ? 'sort_order' : 'id', static::getModel() === BookingRequest::class ? 'desc' : 'asc');
        if (in_array('sort_order', $fields)) {
            $table->reorderable('sort_order');
        }

        return $table;
    }
}
