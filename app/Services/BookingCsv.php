<?php

namespace App\Services;

use App\Filament\CmsFields;
use App\Models\BookingRequest;
use Illuminate\Database\Eloquent\Builder;
use Symfony\Component\HttpFoundation\StreamedResponse;

class BookingCsv
{
    public const HEADERS = ['Номер заявки', 'Дата заявки (Казахстан, UTC+5)', 'Имя клиента', 'Телефон', 'Город', 'Автомобиль', 'Статус', 'Тип заявки', 'Дата получения', 'Дата возврата', 'Комментарий клиента', 'Заметка менеджера', 'Страница заявки', 'Источник рекламы (UTM source)', 'Канал рекламы (UTM medium)', 'Кампания (UTM campaign)'];

    public static function choices(string $field): array
    {
        $definition = collect(CmsFields::definition(BookingRequest::class)['fields'])->firstWhere('column', $field);

        return collect($definition['choices'] ?? [])->mapWithKeys(fn (array $choice): array => [$choice[0] => $choice[1]])->all();
    }

    public static function label(string $field, ?string $value): string
    {
        return self::choices($field)[$value] ?? ($value ?: 'Не указано');
    }

    public static function row(BookingRequest $request): array
    {
        $phone = preg_replace('/\D/', '', $request->phone);
        if (preg_match('/^[78]\d{10}$/', $phone)) {
            $phone = '+7 ('.substr($phone, 1, 3).') '.substr($phone, 4, 3).'-'.substr($phone, 7, 2).'-'.substr($phone, 9, 2);
        } else {
            $phone = $request->phone;
        }

        return array_map(function (mixed $value): string {
            $text = (string) $value;

            return preg_match('/^[\s]*[=+\-@]/u', $text) ? "'".$text : $text;
        }, [
            $request->id,
            $request->created_at?->copy()->timezone('Asia/Almaty')->format('d.m.Y H:i') ?? 'Не указана',
            $request->name,
            $phone,
            $request->city?->name ?? 'Не указан',
            $request->car?->name ?? 'Не выбран',
            self::label('status', $request->status),
            self::label('kind', $request->kind),
            $request->start_date?->format('d.m.Y') ?? 'Не указана',
            $request->end_date?->format('d.m.Y') ?? 'Не указана',
            $request->comment,
            $request->manager_note,
            $request->source_page,
            $request->utm_source,
            $request->utm_medium,
            $request->utm_campaign,
        ]);
    }

    public static function download(Builder $query): StreamedResponse
    {
        return response()->streamDownload(function () use ($query): void {
            $output = fopen('php://output', 'w');
            fwrite($output, "\xEF\xBB\xBF");
            fputcsv($output, self::HEADERS, ';', '"', '');
            foreach ($query->with(['city', 'car'])->lazy(500) as $request) {
                fputcsv($output, self::row($request), ';', '"', '');
            }
            fclose($output);
        }, 'legion-leads.csv', ['Content-Type' => 'text/csv; charset=utf-8']);
    }
}
