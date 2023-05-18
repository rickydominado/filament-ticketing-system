<?php

namespace App\Filament\Tables\Actions;

use Filament\Tables\Actions\RestoreAction as BaseComponent;

class RestoreAction extends BaseComponent
{
    public function getSuccessNotificationTitle(): ?string
    {
        return 'Ticket restored successfully!';
    }
}
