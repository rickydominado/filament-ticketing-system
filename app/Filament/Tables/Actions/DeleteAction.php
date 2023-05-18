<?php

namespace App\Filament\Tables\Actions;

use App\Events\UpdateNotificationBadgeCountEvent;
use Filament\Support\Actions\Concerns\CanCustomizeProcess;
use Filament\Tables\Actions\Action;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\DatabaseNotification;

class DeleteAction extends Action
{
    use CanCustomizeProcess;

    public static function getDefaultName(): ?string
    {
        return 'delete';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament-support::actions/delete.single.label'));

        $this->modalHeading(fn (): string => __('filament-support::actions/delete.single.modal.heading', ['label' => $this->getRecordTitle()]));

        $this->modalButton(__('filament-support::actions/delete.single.modal.actions.delete.label'));

        $this->successNotificationTitle(__('filament-support::actions/delete.single.messages.deleted'));

        $this->color('danger');

        $this->icon('heroicon-s-trash');

        $this->requiresConfirmation();

        $this->hidden(static function (Model $record): bool {
            if (!method_exists($record, 'trashed')) {
                return false;
            }

            return $record->trashed();
        });

        $this->action(function (): void {
            $this->process(static function (Model $record) {
                $notifications = DatabaseNotification::whereNull('read_at')->get();

                foreach ($notifications as $notification) {
                    if ($notification['data']['viewData']['inquiry_id'] === $record->id) {
                        $notification->delete();
                    }
                }
            });

            $result = $this->process(static fn (Model $record) => $record->delete());

            if (!$result) {
                $this->failure();

                return;
            }

            UpdateNotificationBadgeCountEvent::dispatch(auth()->user());

            $this->success();
        });
    }

    public function getSuccessNotificationTitle(): ?string
    {
        return 'Ticket deleted successfully!';
    }
}
