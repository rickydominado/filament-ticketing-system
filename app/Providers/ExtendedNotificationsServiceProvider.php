<?php

namespace App\Providers;

use App\Filament\Notifications\Http\Livewire\Notifications;
use Filament\Notifications\NotificationsServiceProvider;
use Filament\Notifications\Testing\TestsNotifications;
use Livewire\Component;
use Livewire\Livewire;
use Livewire\Response;
use Livewire\Testing\TestableLivewire;
use Spatie\LaravelPackageTools\Package;

class ExtendedNotificationsServiceProvider extends NotificationsServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package->name('notifications');
    }

    public function packageBooted(): void
    {
        Livewire::component('notifications', Notifications::class);

        Livewire::listen('component.dehydrate', function (Component $component, Response $response): Response {
            if (!Livewire::isLivewireRequest()) {
                return $response;
            }

            if ($component->redirectTo !== null) {
                return $response;
            }

            if (count(session()->get('filament.notifications') ?? []) > 0) {
                $component->emit('notificationsSent');
            }

            return $response;
        });

        TestableLivewire::mixin(new TestsNotifications());
    }
}
