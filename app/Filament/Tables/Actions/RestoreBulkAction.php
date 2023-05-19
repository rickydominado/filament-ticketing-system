<?php

namespace App\Filament\Tables\Actions;

use Filament\Tables\Actions\RestoreBulkAction as BaseAction;

class RestoreBulkAction extends BaseAction
{
    public function getSuccessNotificationTitle(): ?string
    {
        return 'Ticket(s) restored successfully!';
    }
}
