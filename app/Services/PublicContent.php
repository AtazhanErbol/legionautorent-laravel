<?php

namespace App\Services;

use App\Models\CarCategory;
use App\Models\City;
use App\Models\CmsModel;
use App\Models\InterfaceText;
use App\Models\MenuLink;
use App\Models\Page;
use App\Models\SiteSection;
use App\Models\SiteSettings;
use App\Models\Translation;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class PublicContent
{
    public static function key(): string
    {
        $connection = config('database.connections.'.config('database.default'));

        return 'legion-content-v2:'.hash('sha256', implode('|', [$connection['host'], $connection['port'], $connection['database']]));
    }

    private static function snapshot(CmsModel $model): array
    {
        return [
            'attributes' => $model->getAttributes(),
            'translations' => $model->relationLoaded('translations')
                ? $model->translations->map(fn (Translation $translation) => $translation->getAttributes())->all() : [],
        ];
    }

    private static function models(string $class, array $records): Collection
    {
        return new Collection(array_map(function (array $record) use ($class): CmsModel {
            $model = (new $class)->newFromBuilder($record['attributes']);
            $model->setRelation('translations', Translation::hydrate($record['translations']));

            return $model;
        }, $records));
    }

    public static function all(): array
    {
        $current = request()->attributes->get('legion_public_content');
        if ($current !== null) {
            return $current;
        }
        $data = Cache::remember(self::key(), 300, function (): array {
            $collections = [
                'site' => new Collection([SiteSettings::with('translations')->findOrFail(1)]),
                'cities' => City::where('active', true)->with('translations')->orderBy('sort_order')->orderBy('name')->get(),
                'categories' => CarCategory::where('active', true)->with('translations')->orderBy('sort_order')->get(),
                'pages' => Page::where('active', true)->where('show_in_footer', true)->with('translations')->get(),
                'menu' => MenuLink::where('active', true)->orderBy('sort_order')->orderBy('id')->get(),
                'sections' => SiteSection::where('active', true)->orderBy('sort_order')->orderBy('id')->get(),
                'texts' => InterfaceText::all(),
            ];

            return array_map(fn (Collection $records) => $records->map(fn (CmsModel $model) => self::snapshot($model))->all(), $collections);
        });
        $result = [];
        foreach (['site' => SiteSettings::class, 'cities' => City::class, 'categories' => CarCategory::class, 'pages' => Page::class, 'menu' => MenuLink::class, 'sections' => SiteSection::class, 'texts' => InterfaceText::class] as $key => $class) {
            $result[$key] = self::models($class, $data[$key]);
        }
        $result['site'] = $result['site']->first();
        $result['texts'] = $result['texts']->keyBy('source');
        request()->attributes->set('legion_public_content', $result);

        return $result;
    }

    public static function forget(): void
    {
        request()->attributes->remove('legion_public_content');
        request()->attributes->remove('interface_copy');
        Cache::forget(self::key());
        if (DB::transactionLevel() > 0) {
            DB::afterCommit(fn () => Cache::forget(self::key()));
        }
    }
}
