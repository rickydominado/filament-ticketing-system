<?php

namespace App\Filament\Tables\Actions;

use Filament\Tables\Actions\RestoreBulkAction as BaseComponent;

class RestoreBulkAction extends BaseComponent
{
    public function getSuccessNotificationTitle(): ?string
    {
        return 'Ticket(s) restored successfully!';
    }
}
