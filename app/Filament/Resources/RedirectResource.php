<?php

namespace App\Filament\Resources;

use App\Filament\CmsFields;
use App\Filament\Resources\Pages\CreateRedirect;
use App\Filament\Resources\Pages\EditRedirect;
use App\Filament\Resources\Pages\ListRedirect;
use App\Models\Redirect;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class RedirectResource extends CmsResource
{
    protected static bool $isDiscovered = true;

    protected static ?string $model = Redirect::class;

    protected static ?string $slug = 'redirects';

    protected static string|\UnitEnum|null $navigationGroup = 'Переводы и SEO';

    protected static ?string $modelLabel = 'Перенаправление';

    protected static ?string $pluralModelLabel = 'Перенаправления';

    protected static ?int $navigationSort = 18;

    public static function form(Schema $schema): Schema
    {
        $fields = CmsFields::inputs(Redirect::class);
        foreach ($fields as $field) {
            match ($field->getName()) {
                'old_path' => $field->label('Старый адрес')->placeholder('/old-car/')
                    ->helperText('Адрес, который больше не используется. Укажите путь без домена, например /old-car/.'),
                'new_path' => $field->label('Новый адрес')->placeholder('/car/new-car')
                    ->helperText('Адрес существующей страницы на сайте, куда нужно отправить посетителя. Например /car/new-car.'),
                'status_code' => $field->label('Тип перенаправления')
                    ->options([301 => '301 — адрес изменился постоянно', 302 => '302 — временное перенаправление'])
                    ->helperText('При постоянной смене адреса оставьте 301: это помогает перенести поисковые сигналы на новую страницу.'),
                'active' => $field->label('Перенаправление включено'),
                default => null,
            };
        }

        return $schema->components([
            Section::make('Переход со старой ссылки на новую')
                ->description('Используйте этот раздел после смены адреса страницы. Например: раньше /old-car/, теперь /car/new-car. Посетитель по старой ссылке автоматически попадёт на новую страницу. При обычном заполнении карточки ничего создавать здесь не нужно.')
                ->schema($fields)->columns(['default' => 1, 'lg' => 2]),
        ]);
    }

    public static function getPages(): array
    {
        return ['index' => ListRedirect::route('/'), 'create' => CreateRedirect::route('/create'), 'edit' => EditRedirect::route('/{record}/edit')];
    }
}
