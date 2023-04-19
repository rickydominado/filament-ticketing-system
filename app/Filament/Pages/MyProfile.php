<?php

namespace App\Filament\Pages;

use JeffGreco13\FilamentBreezy\Pages\MyProfile as BaseProfile;

class MyProfile extends BaseProfile
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.my-profile';

    protected function getUpdateProfileFormSchema(): array
    {
        return [
            \Filament\Forms\Components\FileUpload::make('profile_photo_path')
                ->image()
                ->avatar()
                ->disk($this->user->profilePhotoDisk())
                ->directory($this->user->profilePhotoDirectory())
                ->rules(['nullable', 'mimes:jpg,jpeg,png', 'max:1024']),

            \Filament\Forms\Components\TextInput::make('firstname')
                ->label(__('filament::pages/my-profile.fields.firstname.label')),

            \Filament\Forms\Components\TextInput::make('lastname')
                ->label(__('filament::pages/my-profile.fields.lastname.label')),

            \Filament\Forms\Components\TextInput::make('email')
                ->required(fn (null|string $state): null|string => !filled($state))
                ->email()
                ->unique(config('filament-breezy.user_model'), ignorable: $this->user)
                ->label(__('filament::pages/my-profile.fields.email.label'))
                ->placeholder('Email Address'),

            \Filament\Forms\Components\TextInput::make('address')
                ->required(fn (null|string $state): null|string => !filled($state))
                ->label(__('filament::pages/my-profile.fields.address.label'))
                ->placeholder('Address'),

            \Filament\Forms\Components\TextInput::make('mobile_number')
                ->required(fn (null|string $state): null|string => !filled($state))
                ->tel()
                ->label(__('filament::pages/my-profile.fields.mobile-number.label'))
                ->placeholder('+63(000)000-00-00')
                ->mask(fn (\Filament\Forms\Components\TextInput\Mask $mask) => $mask->pattern('+{63}(000)000-00-00')),
        ];
    }

    public function updateProfile()
    {
        parent::updateProfile();

        return redirect()->route('filament.pages.my-profile');
    }
}