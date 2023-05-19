<?php

namespace App\Filament\Tables\Actions;

use Filament\Tables\Actions\ForceDeleteBulkAction as BaseAction;

class ForceDeleteBulkAction extends BaseAction
{
    public function getSuccessNotificationTitle(): ?string
    {
        return 'Ticket(s) deleted permanently!';
    }
}
