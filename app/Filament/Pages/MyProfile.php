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
                // ->label(__('filament-breezy::default.fields.name')),
                ->label('Firstname'),

            \Filament\Forms\Components\TextInput::make('lastname')
                ->label('Lastname'),

            \Filament\Forms\Components\TextInput::make('email')
                ->required(fn (null|string $state): null|string => !filled($state))
                ->email()
                ->unique(config('filament-breezy.user_model'), ignorable: $this->user)
                ->label('Email Address')
                ->placeholder('Email Address'),

            \Filament\Forms\Components\TextInput::make('address')
                ->required(fn (null|string $state): null|string => !filled($state))
                ->label('Address')
                ->placeholder('Address'),

            \Filament\Forms\Components\TextInput::make('mobile_number')
                ->required(fn (null|string $state): null|string => !filled($state))
                ->tel()
                ->label('Mobile Number')
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
