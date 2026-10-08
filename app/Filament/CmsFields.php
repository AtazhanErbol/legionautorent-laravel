<?php

namespace App\Filament;

use App\Models\BookingRequest;
use App\Models\CarImage;
use App\Models\ContentType;
use App\Models\Translation;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
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

    public static function fields(string $model, array $except = []): array
    {
        $d = self::definition($model);
        $groups = ['Основное' => [], 'SEO и ссылки' => [], 'Дополнительно' => []];
        foreach ($d['fields'] as $f) {
            $n = $f['column'];
            $type = $f['type'];
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
                if ($type === 'ImageField') {
                    $field->image();
                } else {
                    $field->acceptedFileTypes(str_contains($n, 'video') ? ['video/mp4'] : ['model/gltf-binary', 'application/octet-stream']);
                }
            } elseif ($f['choices']) {
                $field = Select::make($n)->options(collect($f['choices'])->mapWithKeys(fn ($c) => [$c[0] => $c[1]])->all());
            } elseif ($type === 'JSONField') {
                $field = Textarea::make($n)->rows(5)->formatStateUsing(fn ($state) => json_encode($state ?? [], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE))->dehydrateStateUsing(fn ($state) => json_decode($state ?: '[]', true, 512, JSON_THROW_ON_ERROR));
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
                }if ($f['max_length']) {
                    $field->maxLength($f['max_length']);
                }
            }
            if ($n === 'password') {
                $field->password()->revealable()->minLength(12)->required(fn (string $operation): bool => $operation === 'create')->dehydrated(fn ($state) => filled($state))->formatStateUsing(fn () => null)->dehydrateStateUsing(fn ($state) => Hash::make($state));
            }
            $field->label($f['label'])->helperText($f['help'] ?: null);
            if (! $f['blank'] && ! $f['nullable'] && $type !== 'BooleanField' && $n !== 'password') {
                $field->required();
            }
            if (array_key_exists('default', $f)) {
                $field->default($f['default']);
            }if ($f['unique']) {
                $field->unique(ignoreRecord: true);
            }
            if ($model === BookingRequest::class && ! in_array($n, ['status', 'manager_note'])) {
                $field->disabled()->dehydrated(false);
            }
            $group = str_starts_with($n, 'seo_') || str_starts_with($n, 'og_') || in_array($n, ['canonical_url', 'robots', 'legacy_meta']) ? 'SEO и ссылки' : (str_contains($n, 'hero_') || str_contains($n, 'partner_') || $type === 'JSONField' ? 'Дополнительно' : 'Основное');
            $groups[$group][] = $field;
        }
        foreach ($d['m2m'] as $f) {
            $target = collect(json_decode(file_get_contents(resource_path('data/cms-schema.json')), true))->firstWhere('table', $f['target']);
            $cls = 'App\\Models\\'.$target['model'];
            $groups['Основное'][] = Select::make($f['name'])->label(['cities' => 'Города', 'features' => 'Оснащение', 'groups' => 'Роли', 'user_permissions' => 'Дополнительные права', 'permissions' => 'Права доступа'][$f['name']] ?? $f['name'])->multiple()->relationship($f['name'], self::titleAttribute($cls))->searchable()->forceSearchCaseInsensitive()->preload();
        }
        $sections = [];
        foreach ($groups as $title => $items) {
            if ($items) {
                $sections[] = Section::make($title)->schema($items)->columns(2)->collapsible()->collapsed($title !== 'Основное')->columnSpanFull();
            }
        }

        return $sections;
    }
}
