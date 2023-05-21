<?php

namespace App\Filament\Resources\InquiryResource\Pages;

use App\Events\UpdateNotificationBadgeCountEvent;
use App\Filament\Resources\InquiryResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Notifications\DatabaseNotification;

class EditInquiry extends EditRecord
{
    protected static string $resource = InquiryResource::class;

    public function mount($record): void
    {
        parent::mount($record);

        $unreadNotification = DatabaseNotification::where('data->viewData->inquiry_id', $record)
            ->where('notifiable_id', auth()->user()->id)
            ->get();

        $unreadNotification->markAsRead();

        $notifications = DatabaseNotification::where('notifiable_id', auth()->user()->id)
            ->whereNull('read_at')
            ->count();

        event(new UpdateNotificationBadgeCountEvent(auth()->user(), $notifications));
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
        return $this->getResource()::getUrl('view', ['record' => $this->record]);
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Ticket updated successfully!';
    }
}
