<?php

namespace App\Filament\Resources\InquiryResource\Pages;

use App\Filament\Resources\InquiryResource;
use Closure;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Model;

class ListInquiries extends ListRecords
{
    protected static string $resource = InquiryResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getTableFiltersFormColumns(): int
    {
        return 2;
    }

    protected function getTableRecordClassesUsing(): ?Closure
    {
        return function (Model $record) {
            if ($record->deleted_at) {
                return [
                    'border-l-4 border-yellow-600 bg-gray-100',
                    'dark:border-yellow-600 dark:bg-gray-300/10' => config('tables.dark_mode'),
                ];
            }

            if (!$record->has_notification) {
                return ['hidden'];
            }

            foreach (auth()->user()->unreadNotifications as $notification) {
                if ($notification['data']['viewData']['inquiry_id'] === $record->id) {
                    return [
                        'border-l-4 border-cyan-600 bg-gray-100',
                        'dark:border-cyan-600 dark:bg-gray-300/10' => config('tables.dark_mode'),
                    ];
                }
            }
        };
    }
}
