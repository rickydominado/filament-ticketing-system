<?php

namespace App\Filament\Tables\Actions;

use Filament\Tables\Actions\RestoreAction as BaseAction;

class RestoreAction extends BaseAction
{
    public function getSuccessNotificationTitle(): ?string
    {
        return 'Ticket restored successfully!';
    }
}
