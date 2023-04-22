<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Closure;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Model;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getTableRecordClassesUsing(): ?Closure
    {
        return fn (Model $record) => match ($record->deleted_at) {
            default => [
                'border-l-4 border-yellow-600 bg-gray-100',
                'dark:border-yellow-600 dark:bg-gray-300/10' => config('tables.dark_mode'),
            ],
            null => null
        };
    }
}
