<?php

namespace App\Filament\Tables\Actions;

use App\Events\UpdateNotificationBadgeCountEvent;
use Filament\Support\Actions\Concerns\CanCustomizeProcess;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\TrashedFilter;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\DatabaseNotification;

class DeleteBulkAction extends BulkAction
{
    use CanCustomizeProcess;

    public static function getDefaultName(): ?string
    {
        return 'delete';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament-support::actions/delete.multiple.label'));

        $this->modalHeading(fn (): string => __('filament-support::actions/delete.multiple.modal.heading', ['label' => $this->getPluralModelLabel()]));

        $this->modalButton(__('filament-support::actions/delete.multiple.modal.actions.delete.label'));

        $this->successNotificationTitle(__('filament-support::actions/delete.multiple.messages.deleted'));

        $this->color('danger');

        $this->icon('heroicon-s-trash');

        $this->requiresConfirmation();

        $this->action(function (): void {
            $this->process(static function (Collection $records) {
                $unreadNotifications = DatabaseNotification::whereIn('data->viewData->inquiry_id', $records->pluck('id')->toArray())->get();

                foreach ($unreadNotifications as $unreadNotification) {
                    $unreadNotification->delete();
                }

                $notifications = DatabaseNotification::where('notifiable_id', auth()->user()->id)
                    ->whereNull('read_at')
                    ->count();

                event(new UpdateNotificationBadgeCountEvent(auth()->user(), $notifications));

                $records->each(fn (Model $record) => $record->delete());
            });

            $this->success();
        });

        $this->deselectRecordsAfterCompletion();

        $this->hidden(function (HasTable $livewire): bool {
            $trashedFilterState = $livewire->getTableFilterState(TrashedFilter::class) ?? [];

            if (!array_key_exists('value', $trashedFilterState)) {
                return false;
            }

            if ($trashedFilterState['value']) {
                return false;
            }

            return filled($trashedFilterState['value']);
        });
    }

    public function getSuccessNotificationTitle(): ?string
    {
        return 'Ticket(s) deleted successfully!';
    }
}
