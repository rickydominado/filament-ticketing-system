<?php

namespace App\Providers;

use App\Filament\Http\Livewire\Notifications;
use Filament\FilamentServiceProvider;
use Filament\Http\Livewire\Auth\Login;
use Filament\Http\Livewire\GlobalSearch;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Http\Middleware\MirrorConfigToSubpackages;
use Livewire\Livewire;
use Spatie\LaravelPackageTools\Package;

class ExtendedFilamentServiceProvider extends FilamentServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package->name('filament');
    }

    protected function bootLivewireComponents(): void
    {
        Livewire::addPersistentMiddleware([
            Authenticate::class,
            DispatchServingFilamentEvent::class,
            MirrorConfigToSubpackages::class,
        ]);

        foreach (array_merge($this->livewireComponents, [
            'filament.core.auth.login' => Login::class,
            'filament.core.global-search' => GlobalSearch::class,
            'filament.core.notifications' => Notifications::class,
        ]) as $alias => $class) {
            Livewire::component($alias, $class);
        }
    }
}
