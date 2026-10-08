<?php

namespace App\Filament;

use App\Models\BookingRequest;
use App\Models\Car;
use App\Models\CarImage;
use App\Models\City;
use App\Models\ContentType;
use App\Models\Page;
use App\Models\SiteSettings;
use App\Models\Translation;
use Closure;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Illuminate\Support\Facades\Hash;

class CmsFields
{
    public static function definition(string $model): array
    {
        $table = (new $model)->getTable();

        return collect(json_decode(file_get_contents(resource_path('data/cms-schema.json')), true))->firstWhere('table', $table);
    }

    public static function titleAttribute(string $model): string
    {
        $names = array_column(self::definition($model)['fields'], 'column');
        foreach (['name', 'title', 'question', 'source', 'label', 'key', 'old_path', 'username', 'object_repr', 'model'] as $n) {
            if (in_array($n, $names)) {
                return $n;
            }
        }

        return 'id';
    }

    public static function inputs(string $model, array $except = [], ?array $only = null): array
    {
        $d = self::definition($model);
        $inputs = [];
        foreach ($d['fields'] as $f) {
            $n = $f['column'];
            $type = $f['type'];
            if ($only !== null && ! in_array($n, $only)) {
                continue;
            }
            if ($f['primary'] || in_array($n, [...$except, 'created_at', 'updated_at', 'last_login', 'date_joined', 'image', 'small', 'card_image', 'card_small', 'width', 'height', 'card_width', 'card_height', 'variants', 'content_type_id']) && $model !== Translation::class) {
                continue;
            }if (in_array($n, $except) || $f['primary'] || in_array($n, ['updated_at', 'created_at'])) {
                continue;
            }
            if ($n === 'object_id' && $model === Translation::class) {
                $field = Select::make($n)->options(function (Get $get) {
                    $ct = ContentType::find($get('content_type_id'));
                    $def = collect(json_decode(file_get_contents(resource_path('data/cms-schema.json')), true))->firstWhere('label', ($ct?->app_label ?? '').'.'.($ct?->model ?? ''));
                    if (! $def || $def['auto']) {
                        return [];
                    }$cls = 'App\\Models\\'.$def['model'];

                    return $cls::query()->pluck(self::titleAttribute($cls), 'id')->all();
                })->searchable()->forceSearchCaseInsensitive()->required();
            } elseif (isset($f['related_model'])) {
                $rel = $f['name'];
                $cls = 'App\\Models\\'.$f['related_model'];
                $field = Select::make($n)->relationship($rel, self::titleAttribute($cls))->searchable()->forceSearchCaseInsensitive()->preload();
                if ($n === 'content_type_id') {
                    $field->live();
                }
            } elseif ($type === 'BooleanField') {
                $field = Toggle::make($n);
            } elseif (in_array($type, ['FileField', 'ImageField'])) {
                $field = FileUpload::make($n)->disk('media')->directory($model === CarImage::class ? 'cars/originals' : 'site')->visibility('public')->maxSize(str_contains($n, 'video') ? 20480 : 10240);
                if ($type === 'ImageField' || (! str_contains($n, 'video') && $n !== 'hero_model')) {
                    $field->image()->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'image/avif'])->rules(['dimensions:max_width=6000,max_height=6000'])->imagePreviewHeight('180');
                } else {
                    $field->acceptedFileTypes(str_contains($n, 'video') ? ['video/mp4'] : ['model/gltf-binary', 'application/octet-stream']);
                }
            } elseif ($f['choices']) {
                $field = Select::make($n)->options(collect($f['choices'])->mapWithKeys(fn ($c) => [$c[0] => $c[1]])->all());
            } elseif ($type === 'JSONField') {
                $field = Textarea::make($n)->rows(5)->rules(['json', fn (): Closure => function (string $attribute, mixed $value, Closure $fail): void {
                    if (filled($value) && json_validate($value) && ! is_array(json_decode($value, true))) {
                        $fail('Укажите JSON-объект или список, а не отдельное число или строку.');
                    }
                }])->validationMessages(['json' => 'Проверьте JSON: ключи и строки должны быть в двойных кавычках.'])->formatStateUsing(fn ($state) => json_encode($state ?? [], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE))->dehydrateStateUsing(fn ($state) => json_decode($state ?: '[]', true, 512, JSON_THROW_ON_ERROR));
            } elseif ($type === 'TextField' && in_array($n, ['body', 'content', 'answer'])) {
                $field = RichEditor::make($n)->toolbarButtons(['bold', 'italic', 'link', 'h2', 'h3', 'bulletList', 'orderedList', 'undo', 'redo'])->columnSpanFull();
            } elseif ($type === 'TextField') {
                $field = Textarea::make($n)->rows(in_array($n, ['body', 'content', 'description']) ? 8 : 3)->columnSpanFull();
            } elseif ($type === 'DateField') {
                $field = DatePicker::make($n);
            } elseif ($type === 'DateTimeField') {
                $field = DateTimePicker::make($n)->timezone(config('legion.display_timezone'));
            } else {
                $field = TextInput::make($n);
                if (str_contains($type, 'Integer') || $type === 'DecimalField') {
                    $field->numeric();
                    if (str_contains($type, 'Integer')) {
                        $field->integer();
                    }
                    if (str_contains($type, 'Positive') || in_array($n, ['base_price', 'daily_price', 'deposit', 'mileage_limit'])) {
                        $field->minValue(0);
                    }
                }if ($f['max_length']) {
                    $field->maxLength($f['max_length']);
                }
            }
            if ($n === 'password') {
                $field->password()->revealable()->minLength(12)->required(fn (string $operation): bool => $operation === 'create')->dehydrated(fn ($state) => filled($state))->formatStateUsing(fn () => null)->dehydrateStateUsing(fn ($state) => Hash::make($state));
            }
            $labels = ['canonical_url' => 'Канонический адрес', 'legacy_id' => 'ID исходного сайта', 'hero_model' => 'Файл прежней 3D-модели', 'hero_model_path' => 'Путь прежней 3D-модели', 'hero_model_license' => 'Лицензия 3D-модели', 'hero_names' => 'Имена объектов 3D-модели', 'enable_hero_3d' => 'Прежний режим 3D', 'hero_placeholder' => 'Резервная 3D-модель', 'instagram' => 'Ссылка на Instagram'];
            $hints = ['slug' => 'Часть адреса страницы. После публикации меняйте только вместе с SEO-перенаправлением.', 'legacy_path' => 'Существующий адрес сохраняйте без изменений.', 'canonical_url' => 'Оставьте пустым для собственного адреса страницы.', 'legacy_meta' => 'Исходные метаданные. Обычно менять не требуется.', 'original' => 'JPG, PNG, WebP или AVIF, до 10 МБ и 6000 px по каждой стороне. Размеры для сайта создаются автоматически.', 'is_main' => 'В галерее выберите одно главное фото.', 'ga4_id' => 'Справочный ID. GA4 должен быть настроен внутри GTM.', 'metrika_id' => 'Справочный ID. Метрика должна быть настроена внутри GTM.'];
            $field->label($labels[$n] ?? $f['label'])->helperText($hints[$n] ?? ($f['help'] ?: null));
            if (! $f['blank'] && ! $f['nullable'] && $type !== 'BooleanField' && $n !== 'password') {
                $field->required();
            }
            if (array_key_exists('default', $f)) {
                $field->default($f['default']);
            }if ($f['unique']) {
                $field->unique(ignoreRecord: true);
            }
            if (($model === Page::class && $n === 'path') || (in_array($model, [Car::class, City::class]) && $n === 'legacy_path')) {
                $prefix = $model === Car::class ? '/car/' : '/';
                $suffix = $model === Car::class ? '' : '/';
                $field->required(false)->placeholder(fn (Get $get): string => $prefix.($get('slug') ?: 'slug').$suffix)
                    ->helperText('Можно оставить пустым: адрес создастся из slug. Существующий адрес сохраняется; менять его следует вместе с SEO-перенаправлением.');
            }
            if ($model === BookingRequest::class && ! in_array($n, ['status', 'manager_note'])) {
                $field->disabled()->dehydrated(false);
            }
            if ($model === Translation::class && ! in_array($n, ['content_type_id', 'object_id', 'language', 'published'])) {
                $field->visible(fn (Get $get): bool => ! $get('content_type_id') || in_array($n, self::translationFields(ContentType::find($get('content_type_id'))?->model ?? '')));
            }
            $inputs[] = $field;
        }
        foreach ($d['m2m'] as $f) {
            if (in_array($f['name'], $except) || ($only !== null && ! in_array($f['name'], $only))) {
                continue;
            }
            $target = collect(json_decode(file_get_contents(resource_path('data/cms-schema.json')), true))->firstWhere('table', $f['target']);
            $cls = 'App\\Models\\'.$target['model'];
            $inputs[] = Select::make($f['name'])->label(['cities' => 'Города', 'features' => 'Оснащение', 'groups' => 'Роли', 'user_permissions' => 'Дополнительные права', 'permissions' => 'Права доступа'][$f['name']] ?? $f['name'])->multiple()->relationship($f['name'], self::titleAttribute($cls))->searchable()->forceSearchCaseInsensitive()->preload();
        }

        return $inputs;
    }

    public static function translationFields(string $type): array
    {
        $seo = ['name', 'title', 'description', 'h1', 'content', 'og_title', 'og_description'];

        return match ($type) {
            'car' => [...$seo, 'fuel', 'color'],
            'city' => [...$seo, 'address', 'hours', 'hero_text'],
            'carcategory' => $seo,
            'page' => [...$seo, 'intro'],
            'faq', 'contentblock' => ['title', 'content'],
            'carimage' => ['name', 'caption'],
            'carspecification' => ['name', 'content'],
            'sitesettings' => ['name', 'address', 'hours', 'whatsapp_message', 'hero_title', 'hero_text', 'hero_price_caption', 'hero_steps_caption', 'partner_title', 'partner_description', 'partner_whatsapp_message', 'footer_text'],
            default => [],
        };
    }

    public static function fields(string $model, array $except = []): array
    {
        $groups = [];
        foreach (self::inputs($model, $except) as $field) {
            $name = $field->getName();
            $group = match (true) {
                $model === SiteSettings::class && str_starts_with($name, 'partner_') || $name === 'show_partner_section' => 'Партнёрство',

                str_starts_with($name, 'seo_') || str_starts_with($name, 'og_') || in_array($name, ['robots', 'canonical_url', 'default_seo_title', 'default_seo_description', 'gtm_id', 'ga4_id', 'metrika_id', 'google_verification', 'yandex_verification']) => 'SEO и аналитика',
                in_array($name, ['legacy_meta', 'legacy_id', 'hero_model', 'hero_model_path', 'hero_model_license', 'hero_names', 'enable_hero_3d', 'hero_placeholder']) => 'Технические данные',
                $model === SiteSettings::class && str_starts_with($name, 'hero_') => 'Первый экран и анимация',
                default => 'Основное',
            };
            $groups[$group][] = $field;
        }
        $sections = [];
        foreach ($groups as $title => $items) {
            $sections[] = Section::make($title)->schema($items)->columns(['default' => 1, 'lg' => 2])->collapsible()->collapsed($title !== 'Основное')->columnSpanFull();
        }

        return $sections;
    }
}
