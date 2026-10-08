<?php

namespace App\Filament\Resources\Pages;

use Filament\Resources\Pages\EditRecord;
use Illuminate\Validation\ValidationException;

abstract class CmsEditRecord extends EditRecord
{
    public function save(bool $shouldRedirect = true, bool $shouldSendSavedNotification = true): void
    {
        try {
            parent::save($shouldRedirect, $shouldSendSavedNotification);
        } catch (ValidationException $exception) {
            $prefix = $this->form->getStatePath().'.';
            $messages = [];
            if (collect(array_keys($exception->errors()))->every(fn (string $field): bool => str_starts_with($field, $prefix))) {
                throw $exception;
            }
            foreach ($exception->errors() as $field => $errors) {
                $messages[str_starts_with($field, $prefix) ? $field : $prefix.$field] = $errors;
            }
            throw ValidationException::withMessages($messages);
        }
    }
}
