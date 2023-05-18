<?php

namespace App\Observers;

use App\Models\Inquiry;
use App\Models\User;
use App\Notifications\InquiryCreateNotification;
use Filament\Notifications\Events\DatabaseNotificationsSent;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;

class InquiryObserver
{
    /**
     * Handle the Inquiry "created" event.
     */
    public function created(Inquiry $inquiry): void
    {
        $view_inquiry_url = URL::signedRoute('inquiry.view-inquiry', ['inquiry' => $inquiry]);

        $info = [
            'inquiry' => $inquiry,
            'view_inquiry_url' => $view_inquiry_url,
        ];

        $users = User::role(['super-admin', 'admin'])->get();

        Notification::send($users, new InquiryCreateNotification($info));
        Notification::route('mail', $inquiry->email)->notify(new InquiryCreateNotification($info));

        // filament admin panel database broadcast notification
        $delay = now()->addSeconds(5);

        foreach ($users as $user) {
            $user->notify(
                (FilamentNotification::make()->title('A new ticket has been created')
                    ->warning()
                    ->body('Check the **notification bell** for more info.')
                    ->duration(5000)
                    ->viewData(['inquiry_id' => $inquiry->id])
                    ->toBroadcast())
                    ->delay($delay)
            );

            event(new DatabaseNotificationsSent($user));
        }
    }

    /**
     * Handle the Inquiry "updated" event.
     */
    public function updated(Inquiry $inquiry): void
    {
        //
    }

    /**
     * Handle the Inquiry "deleted" event.
     */
    public function deleted(Inquiry $inquiry): void
    {
        //
    }

    /**
     * Handle the Inquiry "restored" event.
     */
    public function restored(Inquiry $inquiry): void
    {
        //
    }

    /**
     * Handle the Inquiry "force deleted" event.
     */
    public function forceDeleted(Inquiry $inquiry): void
    {
        //
    }
}
