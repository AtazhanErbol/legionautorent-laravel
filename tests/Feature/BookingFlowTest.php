<?php

namespace Tests\Feature;

use App\Http\Controllers\BookingController;
use App\Models\BookingRequest;
use App\Models\Car;
use App\Models\City;
use Tests\CatalogueTestCase;

class BookingFlowTest extends CatalogueTestCase
{
    public function test_valid_callback_preserves_consent_and_attribution(): void
    {
        $city = City::where('slug', 'kostanay')->first();
        $this->get('/contacts/?city=kostanay&utm_source=review');
        $this->post('/callback/', ['city' => $city->id, 'name' => 'Тестовая заявка', 'phone' => '+7 700 111 22 33', 'consent' => 'on', 'source_token' => BookingController::token('/contacts/')])->assertRedirect('/request-success/');
        $lead = BookingRequest::first();
        $this->assertSame('callback', $lead->kind);
        $this->assertSame('/contacts/', $lead->source_page);
        $this->assertSame('review', $lead->utm_source);
        $this->assertTrue($lead->consent);
        $this->assertSame(BookingController::CONSENT, $lead->consent_text);
    }

    public function test_missing_consent_and_honeypot_do_not_create_leads(): void
    {
        $city = City::first();
        $this->post('/callback/', ['city' => $city->id, 'name' => 'Тест', 'phone' => '+77001112233', 'website' => 'bot', 'source_token' => BookingController::token('/contacts/')])->assertStatus(400);
        $this->assertSame(0, BookingRequest::count());
    }

    public function test_wrong_city_and_expired_source_are_rejected(): void
    {
        $car = Car::first();
        $city = City::whereDoesntHave('cars', fn ($q) => $q->where('cars_car.id', $car->id))->first();
        $this->post('/booking/', ['city' => $city->id, 'car' => $car->id, 'name' => 'Тест', 'phone' => '+77001112233', 'consent' => 'on', 'source_token' => BookingController::token($car->legacy_path)])->assertStatus(400);
        $this->post('/callback/', ['city' => $city->id, 'name' => 'Тест', 'phone' => '+77001112233', 'consent' => 'on', 'source_token' => 'invalid-token'])->assertStatus(400);
        $this->assertSame(0, BookingRequest::count());
    }
}
