<?php

namespace App\Filament\Resources\Pages;

use Filament\Resources\Pages\CreateRecord;
use Illuminate\Validation\ValidationException;

abstract class CmsCreateRecord extends CreateRecord
{
    public function create(bool $another = false): void
    {
        try {
            parent::create($another);
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
