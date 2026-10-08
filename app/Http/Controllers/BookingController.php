<?php

namespace App\Http\Controllers;

use App\Models\BookingRequest;
use App\Models\Car;
use App\Models\City;
use App\Services\CmsValidation;
use App\Services\Seo;
use App\Support\PublicForm;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\RateLimiter;

class BookingController extends Controller
{
    const CONSENT = 'Я согласен на обработку имени, телефона, дат аренды и комментария компанией LEGIONAUTORENT для ответа на мою заявку.';

    public static function token(string $path): string
    {
        return Crypt::encryptString(json_encode(['path' => $path, 'issued' => time()]));
    }

    public function show(Request $r, bool $callback = false, array $errors = [], int $status = 200): Response
    {
        $car = $r->query('car') ? Car::public()->find($r->query('car')) : null;
        $selected = $r->attributes->get('selected_city');
        $city = $selected && (! $car || $car->cities->contains('id', $selected->id)) ? $selected : ($car?->cities->firstWhere('active', true) ?? City::where('active', true)->orderBy('sort_order')->first());
        $initial = ['car' => $car?->id, 'city' => $city?->id, 'source_token' => self::token($car?->legacy_path ?? '/cars/')];

        return SiteController::render('booking', ['seo' => Seo::page(title: site_text('Заявка на аренду | LEGIONAUTORENT'), h1: site_text($callback ? 'Заказать звонок' : 'Забронировать автомобиль'), noindex: true), 'callback' => $callback, 'form' => new PublicForm($callback ? 'callback' : 'booking', array_replace($initial, $r->isMethod('POST') ? $r->except('_token') : []), $errors), 'selected_car' => $car], $status);
    }

    public function store(Request $r): \Symfony\Component\HttpFoundation\Response
    {
        $callback = str_ends_with(rtrim($r->getPathInfo(), '/'), '/callback');
        $key = 'lead:'.hash('sha256', $r->ip());
        if (RateLimiter::tooManyAttempts($key, 5)) {
            return $this->show($r, $callback, ['_global' => ['Слишком много попыток. Попробуйте через 15 минут.']], 429);
        }RateLimiter::hit($key, 900);
        $v = validator($r->all(), ['name' => 'required|string|max:120', 'phone' => ['required', 'string', 'max:30', 'regex:/^[+()\d\s-]+$/'], 'city' => 'required|exists:locations_city,id', 'car' => $callback ? 'nullable' : 'nullable|exists:cars_car,id', 'comment' => 'nullable|string|max:2000', 'consent' => 'accepted', 'website' => 'nullable|size:0', 'source_token' => 'required|string', 'start_date' => 'nullable|date|after_or_equal:today', 'end_date' => 'nullable|date|after:start_date']);
        $v->after(function ($v) use ($r, $callback) {
            $digits = preg_replace('/\D/', '', (string) $r->phone);
            if (strlen($digits) < 10 || strlen($digits) > 15) {
                $v->errors()->add('phone', site_text('Укажите номер телефона: от 10 до 15 цифр.'));
            }if (! $callback) {
                if ((bool) $r->start_date !== (bool) $r->end_date) {
                    $v->errors()->add('start_date', site_text('Укажите обе даты или оставьте их пустыми.'));
                }if ($r->filled('car') && ! Car::public()->where('id', $r->car)->where('accepts_requests', true)->whereHas('cities', fn ($q) => $q->where('locations_city.id', $r->city)->where('active', true))->exists()) {
                    $v->errors()->add('car', site_text('Этот автомобиль не представлен в выбранном городе.'));
                }
            }if (! City::where('active', true)->where('id', $r->city)->exists()) {
                $v->errors()->add('city', site_text('Выберите город'));
            }
        });
        if ($v->fails()) {
            return $this->show($r, $callback, $v->errors()->toArray(), 400);
        }
        try {
            $source = json_decode(Crypt::decryptString($r->source_token), true, 512, JSON_THROW_ON_ERROR);
            if (time() - $source['issued'] > 86400 || ! CmsValidation::path($source['path'])) {
                throw new \RuntimeException;
            }
        } catch (\Throwable) {
            return $this->show($r, $callback, ['_global' => [site_text('Срок действия формы истёк. Обновите страницу.')]], 400);
        }
        $data = ['name' => $r->name, 'phone' => '+'.preg_replace('/\D/', '', $r->phone), 'city_id' => $r->city, 'car_id' => $callback ? null : ($r->car ?: null), 'comment' => $r->comment ?? '', 'consent' => true, 'consent_text' => site_text(self::CONSENT), 'source_page' => language_url($source['path']), 'kind' => $callback ? 'callback' : 'booking', 'status' => 'NEW', 'start_date' => $callback ? null : ($r->start_date ?: null), 'end_date' => $callback ? null : ($r->end_date ?: null), ...$r->session()->get('attribution', [])];
        BookingRequest::create($data);
        $r->session()->put('booking_success', true);

        return redirect(language_url('/request-success/'));
    }
}
