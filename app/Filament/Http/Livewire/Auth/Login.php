<?php

namespace App\Filament\Http\Livewire\Auth;

use Filament\Forms\Components\TextInput;
use Illuminate\Support\Arr;
use JeffGreco13\FilamentBreezy\Http\Livewire\Auth\Login as AuthLogin;

class Login extends AuthLogin
{
    protected function getFormSchema(): array
    {
        $parentSchema = parent::getFormSchema();

        // Pop off the email field and replace it with loginColumn
        unset($parentSchema[0]);

        $parentSchema = Arr::prepend(
            $parentSchema,
            TextInput::make($this->loginColumn)
                ->label(__('filament::login.fields.username.label'))
                ->required()
                ->autocomplete()
        );

        return $parentSchema;
    }
}
