<?php

namespace Tests\Feature;

use App\Filament\Resources\Pages\ListBookingRequest;
use App\Models\BookingRequest;
use App\Models\Car;
use App\Models\City;
use App\Services\BookingCsv;
use Filament\Actions\Testing\TestAction;
use Filament\Facades\Filament;
use Illuminate\Support\Carbon;
use Livewire\Livewire;
use Tests\CatalogueTestCase;

class BookingCsvExportTest extends CatalogueTestCase
{
    public function test_export_uses_readable_labels_relations_and_kazakhstan_time(): void
    {
        $this->travelTo(Carbon::parse('2026-10-09 09:00:00', 'UTC'));
        $car = Car::with('cities')->first();
        $lead = BookingRequest::create(['car_id' => $car->id, 'city_id' => $car->cities->first()->id, 'name' => 'Иван', 'phone' => '+77012345678', 'status' => 'CONTACTED', 'kind' => 'booking', 'consent' => true, 'created_at' => '2026-10-09 09:00:00', 'comment' => 'Текст; с разделителем и "кавычками"']);
        $response = BookingCsv::download(BookingRequest::whereKey($lead->id));
        ob_start();
        $response->sendContent();
        $content = ob_get_clean();
        $this->assertStringStartsWith("\xEF\xBB\xBF", $content);
        $lines = explode("\n", trim(substr($content, 3)));
        $this->assertSame(BookingCsv::HEADERS, str_getcsv($lines[0], ';', '"', ''));
        $row = str_getcsv($lines[1], ';', '"', '');
        $this->assertSame('09.10.2026 14:00', $row[1]);
        $this->assertSame("'+7 (701) 234-56-78", $row[3]);
        $this->assertSame($car->cities->first()->name, $row[4]);
        $this->assertSame($car->name, $row[5]);
        $this->assertSame('Связались', $row[6]);
        $this->assertSame('Аренда', $row[7]);
        $this->assertSame('Не указана', $row[8]);
        $this->assertSame($lead->comment, $row[10]);
    }

    public function test_callback_without_a_car_or_dates_exports_cleanly_and_escapes_formulas(): void
    {
        $lead = new BookingRequest(['id' => 99, 'name' => '=1+1', 'phone' => '+77012345678', 'status' => 'NEW', 'kind' => 'callback', 'comment' => '  =HYPERLINK("https://example.com")', 'manager_note' => "@test\nСледующая строка"]);
        $lead->setRelation('city', null);
        $lead->setRelation('car', null);
        $row = BookingCsv::row($lead);
        $this->assertSame("'=1+1", $row[2]);
        $this->assertSame('Не указан', $row[4]);
        $this->assertSame('Не выбран', $row[5]);
        $this->assertSame('Новая', $row[6]);
        $this->assertSame('Обратный звонок', $row[7]);
        $this->assertSame("'".$lead->comment, $row[10]);
        $this->assertSame("'".$lead->manager_note, $row[11]);
    }

    public function test_admin_export_download_obeys_status_filter_and_displays_translated_status(): void
    {
        Filament::setCurrentPanel(Filament::getPanel('admin'));
        $this->actingAs($this->owner());
        BookingRequest::create(['city_id' => City::first()->id, 'name' => 'CSV новая', 'phone' => '+77012345678', 'status' => 'NEW', 'kind' => 'callback', 'consent' => true]);
        BookingRequest::create(['city_id' => City::first()->id, 'name' => 'CSV отменена', 'phone' => '+77012345678', 'status' => 'CANCELLED', 'kind' => 'booking', 'consent' => true]);
        $component = Livewire::test(ListBookingRequest::class)
            ->filterTable('status', 'NEW')
            ->assertSee('Новая')
            ->callAction(TestAction::make('csv')->table())
            ->assertFileDownloaded('legion-leads.csv');
        $content = base64_decode($component->effects['download']['content']);
        $this->assertStringContainsString('CSV новая', $content);
        $this->assertStringNotContainsString('CSV отменена', $content);
        $this->assertStringNotContainsString('city_id', $content);
        $this->assertStringNotContainsString('car_id', $content);
    }
}
