<x-filament::page
    :class="\Illuminate\Support\Arr::toCssClasses([
        'filament-resources-list-records-page',
        'filament-resources-' . str_replace('/', '-', $this->getResource()::getSlug()),
    ])"

    x-data="{ userId: {{ auth()->user()->id }} }"

    x-init="
        window.addEventListener('EchoLoaded', () => {
            window.Echo.private(`App.Models.User.${userId}`)
                .listen('.notification-badge-count.sent', () => {
                    setTimeout(() => $wire.call('$refresh'), 500)
            })
        })
    "

    @receive-notification.window="setTimeout(() => $wire.call('$refresh'), 500)"
>
    {{ $this->table }}
</x-filament::page>
