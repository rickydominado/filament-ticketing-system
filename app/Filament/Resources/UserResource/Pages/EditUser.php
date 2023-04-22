<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Models\User;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

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

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $user = User::withTrashed()->find($data['id']);

        $data['role'] = $user->roles()->first()->id;

        return $data;
    }

    protected function handleRecordUpdate(Model $user, array $data): Model
    {
        $user->update($data);

        $user->roles()->sync(array($data['role']));

        return $user;
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'User updated successfully!';
    }
}
