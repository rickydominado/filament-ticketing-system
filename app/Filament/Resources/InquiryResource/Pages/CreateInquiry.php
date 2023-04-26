<?php

namespace App\Filament\Resources\InquiryResource\Pages;

use App\Filament\Resources\InquiryResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateInquiry extends CreateRecord
{
    protected static string $resource = InquiryResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Ticket created successfully!';
    }
}
