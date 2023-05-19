<?php

namespace App\Filament\Resources\InquiryResource\Pages;

use App\Events\UpdateNotificationBadgeCountEvent;
use App\Filament\Resources\InquiryResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditInquiry extends EditRecord
{
    protected static string $resource = InquiryResource::class;

    public function mount($record): void
    {
        parent::mount($record);

        foreach (auth()->user()->unreadNotifications as $notification) {
            if ($notification['data']['viewData']['inquiry_id'] === $record) {
                $notification->markAsRead();
            }
        }

        UpdateNotificationBadgeCountEvent::dispatch(auth()->user());
    }

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Ticket updated successfully!';
    }
}
