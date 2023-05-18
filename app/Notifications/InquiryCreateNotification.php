<?php

namespace App\Notifications;

use App\Models\User;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class InquiryCreateNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $inquiry;
    public $view_inquiry_url;

    /**
     * Create a new notification instance.
     */
    public function __construct(array $info)
    {
        $this->inquiry = $info['inquiry'];
        $this->view_inquiry_url = $info['view_inquiry_url'];
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return $this->getMessage();
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }

    public function toDatabase(User $notifiable): array
    {
        return $this->getFilamentDatabaseNotification();
    }

    /**
     * Determine the notification's delivery delay.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function withDelay($notifiable)
    {
        return [
            'mail' => now()->addSeconds(5),
            'database' => now()->addSeconds(5),
        ];
    }

    public function getMessage()
    {
        return (new MailMessage)
            ->subject('A new ticket has been created!')
            ->greeting('Hi,')
            ->line('A New ticket has been created!')
            ->line("Customer: " . $this->inquiry->name)
            ->line("Ticket name: " . $this->inquiry->title)
            ->line("Brief description: " . Str::limit($this->inquiry->content, 200))
            ->action('View full ticket', $this->view_inquiry_url)
            ->line('Thank you')
            ->line(config('app.name') . ' Team')
            ->salutation(' ');
    }

    public function getFilamentDatabaseNotification()
    {
        return FilamentNotification::make()
            ->title("A new ticket has been created.")
            ->actions([
                Action::make('view')
                    ->button()
                    ->url(route('filament.resources.inquiries.edit', ['record' => $this->inquiry->id]), shouldOpenInNewTab: true),
                Action::make('close')
                    ->color('secondary')
                    ->close(),
            ])
            ->persistent()
            ->viewData(['inquiry_id' => $this->inquiry->id])
            ->getDatabaseMessage();
    }
}
