@props([
    'user' => \Filament\Facades\Filament::auth()->user(),
])

<div
    x-data="{ profile: @js(auth()->user()->getProfilePhotoUrlAttribute()) }"

    @profile-update.window="profile = $event.detail.profile"

    {{ $attributes->class([
        'w-10 h-10 rounded-full bg-gray-200 bg-cover bg-center',
        'dark:bg-gray-900' => config('filament.dark_mode'),
    ]) }}

    :style="`background-image: url(${profile})`"
></div>
