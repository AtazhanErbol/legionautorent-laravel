<?php

namespace Tests\Feature;

use App\Filament\Resources\CarBrandResource;
use App\Filament\Resources\CarCategoryResource;
use App\Filament\Resources\CarDiscountResource;
use App\Filament\Resources\CarFeatureResource;
use App\Filament\Resources\CarImageResource;
use App\Filament\Resources\CarPriceResource;
use App\Filament\Resources\CarSpecificationResource;
use App\Filament\Resources\Pages\CreateCar;
use App\Filament\Resources\Pages\EditCar;
use App\Filament\Resources\Pages\EditMenuLink;
use App\Filament\Resources\Pages\EditPage;
use App\Filament\Resources\Pages\EditSiteSettings;
use App\Models\Car;
use App\Models\MenuLink;
use App\Models\Page;
use App\Models\SiteSettings;
use Filament\Facades\Filament;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\CatalogueTestCase;

class AdminWorkflowTest extends CatalogueTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Filament::setCurrentPanel(Filament::getPanel('admin'));
        $this->actingAs($this->owner());
    }

    public function test_child_sections_do_not_clutter_the_navigation(): void
    {
        foreach ([CarImageResource::class, CarDiscountResource::class, CarPriceResource::class, CarSpecificationResource::class, CarBrandResource::class, CarCategoryResource::class, CarFeatureResource::class] as $resource) {
            $this->assertFalse($resource::shouldRegisterNavigation());
        }
    }

    public function test_car_creation_saves_an_uploaded_photo_and_discounts_together(): void
    {
        Storage::fake('media');
        $example = Car::with('cities')->first();
        $component = Livewire::test(CreateCar::class)->fillForm([
            'name' => 'Тестовый автомобиль', 'slug' => 'test-car-unified-form',
            'cities' => $example->cities->first()->id, 'base_price' => 45000,
            'images' => ['new-photo' => ['original' => [], 'alt' => 'Фото тестового автомобиля', 'is_main' => true]],
            'discounts' => [
                'first' => ['label' => '3–7 дней', 'min_days' => 3, 'max_days' => 7, 'percent' => 10],
                'second' => ['label' => '7–15 дней', 'min_days' => 7, 'max_days' => 15, 'percent' => 15],
            ],
        ])->set('data.images.new-photo.original', [UploadedFile::fake()->image('test-main.jpg', 1600, 1000)]);
        $component->call('create')->assertHasNoFormErrors();
        $car = Car::where('slug', 'test-car-unified-form')->firstOrFail();
        $this->assertSame('/car/test-car-unified-form', $car->legacy_path);
        $this->assertSame('Тестовый автомобиль', $car->seo_title);
        $this->assertSame('Аренда Тестовый автомобиль', $car->seo_h1);
        $this->assertCount(1, $car->images);
        $this->assertCount(2, $car->discounts);
        $photo = $car->images->first();
        $this->assertTrue($photo->is_main);
        foreach (['original', 'image', 'small', 'card_image', 'card_small'] as $field) {
            Storage::disk('media')->assertExists($photo->$field);
        }
        $this->assertSame(1200, $photo->width);
        $this->get($car->legacy_path)->assertOk()->assertSee('Фото тестового автомобиля');
    }

    public function test_main_photo_can_be_changed_without_losing_photos_or_seo(): void
    {
        $car = Car::with('images')->first();
        $component = Livewire::test(EditCar::class, ['record' => $car->id]);
        $photos = $component->get('data.images');
        $target = $car->images->firstWhere('is_main', false);
        $this->assertNotNull($target);
        foreach ($photos as $key => &$photo) {
            $photo['is_main'] = $key === 'record-'.$target->id;
        }
        unset($photo);
        $component->fillForm(['images' => $photos, 'description' => 'Описание после смены фото.'])->call('save')->assertHasNoFormErrors();
        $this->assertSame(1, $car->images()->where('is_main', true)->count());
        $this->assertTrue($target->fresh()->is_main);
        $this->assertSame($car->images->count(), $car->images()->count());
        $this->assertSame($car->seo_title, $car->fresh()->seo_title);
        $this->assertSame($car->legacy_path, $car->fresh()->legacy_path);
    }

    public function test_two_main_photos_show_an_inline_error_and_do_not_save_text(): void
    {
        $car = Car::with('images')->first();
        $component = Livewire::test(EditCar::class, ['record' => $car->id]);
        $photos = $component->get('data.images');
        foreach ($photos as &$photo) {
            $photo['is_main'] = true;
        }
        unset($photo);
        $component->fillForm(['images' => $photos, 'description' => 'Should not be saved.'])->call('save')->assertHasFormErrors(['images']);
        $this->assertSame($car->description, $car->fresh()->description);
        $this->assertSame(1, $car->images()->where('is_main', true)->count());
    }

    public function test_overlapping_discounts_show_an_inline_error_before_saving(): void
    {
        $car = Car::first();
        $component = Livewire::test(EditCar::class, ['record' => $car->id]);
        $discounts = $component->get('data.discounts');
        $first = array_key_first($discounts);
        $discounts[$first]['max_days'] = 10;
        $component->fillForm(['discounts' => $discounts, 'description' => 'Should not be saved.'])->call('save')->assertHasFormErrors(['discounts']);
        $this->assertSame($car->description, $car->fresh()->description);
        $this->assertSame(7, $car->discounts()->first()->max_days);
    }

    public function test_all_prices_can_change_in_one_atomic_save(): void
    {
        $car = Car::first();
        $first = ['label' => 'Короткая аренда', 'min_days' => 1, 'max_days' => 6, 'daily_price' => 45000];
        $second = ['label' => 'Длительная аренда', 'min_days' => 7, 'max_days' => null, 'daily_price' => 40000];
        Livewire::test(EditCar::class, ['record' => $car->id])->fillForm(['prices' => ['first' => $first, 'second' => $second]])->call('save')->assertHasNoFormErrors();
        $component = Livewire::test(EditCar::class, ['record' => $car->id]);
        $prices = $component->get('data.prices');
        $keys = array_keys($prices);
        $prices[$keys[0]]['max_days'] = 8;
        $prices[$keys[1]]['min_days'] = 9;
        $component->fillForm(['prices' => $prices])->call('save')->assertHasNoFormErrors();
        $this->assertSame([1, 9], $car->prices()->pluck('min_days')->all());
        $this->assertSame([8, null], $car->prices()->pluck('max_days')->all());
    }

    public function test_invalid_json_is_a_form_error_instead_of_a_500(): void
    {
        $car = Car::first();
        Livewire::test(EditCar::class, ['record' => $car->id])->fillForm(['legacy_meta' => '{broken JSON', 'description' => 'Should not be saved.'])->call('save')->assertHasFormErrors(['legacy_meta' => 'json']);
        $this->assertSame($car->description, $car->fresh()->description);
    }

    public function test_json_scalar_and_fractional_year_are_rejected_before_database_write(): void
    {
        $car = Car::first();
        Livewire::test(EditCar::class, ['record' => $car->id])->fillForm(['legacy_meta' => '123'])->call('save')->assertHasFormErrors(['legacy_meta']);
        Livewire::test(EditCar::class, ['record' => $car->id])->fillForm(['year' => '2021.5'])->call('save')->assertHasFormErrors(['year' => 'integer']);
        $this->assertSame($car->year, $car->fresh()->year);
        $this->assertSame($car->legacy_meta, $car->fresh()->legacy_meta);
    }

    public function test_logo_upload_accepts_an_image_instead_of_a_3d_file(): void
    {
        Storage::fake('media');
        $site = SiteSettings::first();
        Livewire::test(EditSiteSettings::class, ['record' => $site->id])->set('data.logo', [UploadedFile::fake()->image('site-logo.png', 320, 120)])->call('save')->assertHasNoFormErrors();
        $this->assertNotEmpty($site->fresh()->logo);
        Storage::disk('media')->assertExists($site->fresh()->logo);
    }

    public function test_cms_business_rule_errors_are_attached_to_visible_fields(): void
    {
        $menu = MenuLink::first();
        Livewire::test(EditMenuLink::class, ['record' => $menu->id])->fillForm(['path' => '/missing-destination/'])->call('save')->assertHasFormErrors(['path']);
        $this->assertSame($menu->path, $menu->fresh()->path);
        $car = Car::first();
        Livewire::test(EditCar::class, ['record' => $car->id])->fillForm(['canonical_url' => 'https://other-domain.example/car'])->call('save')->assertHasFormErrors(['canonical_url']);
        $this->assertSame($car->canonical_url, $car->fresh()->canonical_url);
    }

    public function test_privacy_text_editor_saves_html_and_keeps_its_url_and_seo(): void
    {
        $page = Page::where('path', '/privacy/')->firstOrFail();
        $body = '<h2>Проверка текста</h2><p>Русский мәтін English <strong>выделение</strong>.</p>';
        Livewire::test(EditPage::class, ['record' => $page->id])->fillForm(['body' => $body])->call('save')->assertHasNoFormErrors();
        $this->assertSame($page->path, $page->fresh()->path);
        $this->assertSame($page->seo_title, $page->fresh()->seo_title);
        $this->get('/privacy/')->assertOk()->assertSee('Русский мәтін English')->assertSee('<strong>выделение</strong>', false);
    }

    public function test_site_settings_can_be_saved_without_altering_the_hero(): void
    {
        $site = SiteSettings::first();
        Livewire::test(EditSiteSettings::class, ['record' => $site->id])->fillForm(['footer_text' => 'Тестовая подпись LEGIONAUTORENT'])->call('save')->assertHasNoFormErrors();
        $this->assertSame($site->hero_video_path, $site->fresh()->hero_video_path);
        $this->assertSame($site->hero_names, $site->fresh()->hero_names);
        $this->get('/')->assertOk()->assertSee('Тестовая подпись LEGIONAUTORENT');
    }
}
