<?php

namespace App\Filament\Resources;

use App\Events\UpdateNotificationBadgeCountEvent;
use App\Filament\Resources\InquiryResource\Pages;
use App\Filament\Resources\InquiryResource\Pages\EditInquiry;
use App\Filament\Resources\InquiryResource\RelationManagers;
use App\Models\Inquiry;
use App\Models\User;
use Carbon\Carbon;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Page;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Notifications\DatabaseNotification;
use Livewire\TemporaryUploadedFile;

class InquiryResource extends Resource
{
    protected static ?string $model = Inquiry::class;

    protected static ?string $navigationIcon = 'heroicon-o-ticket';

    protected static ?string $navigationGroup = 'Inquiry';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Card::make()
                    ->schema([
                        Fieldset::make('User Information')
                            ->schema([
                                TextInput::make('name')
                                    ->autofocus()
                                    ->placeholder('Your Name')
                                    ->disableAutocomplete()
                                    ->minLength(8)
                                    ->maxLength(50)
                                    ->disabled(fn (Page $livewire) => $livewire instanceof EditInquiry),

                                TextInput::make('email')
                                    ->autofocus()
                                    ->placeholder('Your Email')
                                    ->disableAutocomplete()
                                    ->email()
                                    ->minLength(8)
                                    ->maxLength(50)
                                    ->disabled(fn (Page $livewire) => $livewire instanceof EditInquiry),
                            ]),

                        Fieldset::make('Inquiry Information')
                            ->schema([
                                TextInput::make('title')
                                    ->autofocus()
                                    ->placeholder('Inquiry Title')
                                    ->disableAutocomplete()
                                    ->minLength(8)
                                    ->maxLength(50)
                                    ->columnSpan(2)
                                    ->disabled(fn (Page $livewire) => $livewire instanceof EditInquiry),

                                Textarea::make('content')
                                    ->autofocus()
                                    ->placeholder('Inquiry Content...')
                                    ->minLength(10)
                                    ->maxLength(255)
                                    ->columnSpan(2)
                                    ->disabled(fn (Page $livewire) => $livewire instanceof EditInquiry),

                                Grid::make(2)
                                    ->schema([
                                        SpatieMediaLibraryFileUpload::make('image')->collection('images')
                                            ->getUploadedFileNameForStorageUsing(function (TemporaryUploadedFile $file): string {
                                                return $file->getClientOriginalName();
                                            })
                                            ->label('Image(s)')
                                            ->autofocus()
                                            ->disk('media')
                                            ->enableOpen()
                                            ->imagePreviewHeight('50')
                                            ->removeUploadedFileButtonPosition('right')
                                            ->multiple()
                                            ->maxFiles(5)
                                            ->image()
                                            ->rules(['nullable', 'mimes:jpg,jpeg,png', 'max:1024'])
                                            ->hint('max-images : 5 (imgs) | max-image-size : 1 (mb)')
                                            ->hintIcon('heroicon-s-exclamation-circle')
                                            ->disabled(fn (Page $livewire) => $livewire instanceof EditInquiry),
                                    ]),

                                Grid::make(4)
                                    ->schema([
                                        Select::make('status')
                                            ->options(\App\Enums\Inquiry\Status::statuses())
                                            ->autofocus()
                                            ->default(1)
                                            ->disablePlaceholderSelection(),

                                        Select::make('severity')
                                            ->options(\App\Enums\Inquiry\Severity::severities())
                                            ->autofocus()
                                            ->default(1)
                                            ->disablePlaceholderSelection(),
                                    ]),

                                Select::make('category_id')
                                    ->autofocus()
                                    ->placeholder('Select a category')
                                    ->relationship('category', 'name'),

                                Grid::make(2)
                                    ->schema([
                                        Select::make('assigned_to_user_id')
                                            ->label('Assigned to Agent')
                                            ->autofocus()
                                            ->placeholder('Select an agent')
                                            ->options(User::where('is_admin', false)->pluck('fullname', 'id'))
                                            ->required(fn (Page $livewire) => $livewire instanceof EditInquiry)
                                            ->visible(auth()->user()->is_admin),
                                    ]),
                            ])
                            ->columns(3)
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Split::make([
                    TextColumn::make('id')
                        ->sortable()
                        ->limit(8)
                        ->tooltip(self::tooltip())
                        ->grow(false),

                    Stack::make([
                        TextColumn::make('name')
                            ->searchable()
                            ->limit(15)
                            ->tooltip(self::tooltip())
                            ->icon('heroicon-s-user')
                            ->weight('bold'),

                        TextColumn::make('email')
                            ->searchable()
                            ->limit(15)
                            ->tooltip(self::tooltip())
                            ->icon('heroicon-s-mail'),
                    ]),

                    Stack::make([
                        TextColumn::make('title')
                            ->searchable()
                            ->limit(15)
                            ->tooltip(self::tooltip())
                            ->weight('bold'),

                        TextColumn::make('content')
                            ->limit(15)
                            ->tooltip(self::tooltip()),
                    ]),

                    TextColumn::make('category.name'),

                    TextColumn::make('medias_count')
                        ->label('Attachment(s)')
                        ->counts('medias')
                        ->formatStateUsing(fn (string $state): string => $state . ' image(s)')
                        ->icon('heroicon-s-link')
                        ->grow(false),

                    Stack::make([
                        TextColumn::make('status')
                            ->enum(\App\Enums\Inquiry\Status::statuses())
                            ->alignment('center'),

                        TextColumn::make('severity')
                            ->enum(\App\Enums\Inquiry\Severity::severities())
                            ->alignment('center'),
                    ]),

                    Stack::make([
                        TextColumn::make('created_at')
                            ->label('Created_at')
                            ->dateTime('d-M-Y')
                            ->sortable()
                            ->visible(function (?Model $record): bool {
                                if ($record && $record->deleted_at) {
                                    return false;
                                };

                                return true;
                            }),

                        TextColumn::make('deleted_at')
                            ->label('Deleted_at')
                            ->dateTime('d-M-Y')
                            ->sortable()
                            ->visible(function (?Model $record): bool {
                                if ($record && $record->deleted_at) {
                                    return true;
                                };

                                return false;
                            })
                            ->color('warning'),
                    ])->grow(false),
                ]),
            ])
            ->defaultSort('created_at')
            ->filters([
                SelectFilter::make('status')
                    ->options(\App\Enums\Inquiry\Status::statuses())
                    ->indicator('Status'),

                SelectFilter::make('category')->relationship('category', 'name')
                    ->indicator('Category'),

                SelectFilter::make('severity')
                    ->options(\App\Enums\Inquiry\Severity::severities())
                    ->indicator('Severity'),

                TrashedFilter::make()
                    ->visible(auth()->user()->hasRole('super-admin')),

                Filter::make('created_at')
                    ->form([
                        DatePicker::make('created_from')
                            ->placeholder('MM / DD / YYYY')
                            ->closeOnDateSelection(),
                        DatePicker::make('created_until')
                            ->placeholder('MM / DD / YYYY')
                            ->closeOnDateSelection(),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('updated_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('updated_at', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];

                        if ($data['created_from'] ?? null) {
                            $indicators['created_from'] = 'Created from ' . Carbon::parse($data['created_from'])->toFormattedDateString();
                        }

                        if ($data['created_until'] ?? null) {
                            $indicators['created_until'] = 'Created until ' . Carbon::parse($data['created_until'])->toFormattedDateString();
                        }

                        return $indicators;
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                \App\Filament\Tables\Actions\DeleteAction::make(),
                \App\Filament\Tables\Actions\RestoreAction::make(),
            ])
            ->bulkActions([
                \App\Filament\Tables\Actions\DeleteBulkAction::make(),
                \App\Filament\Tables\Actions\ForceDeleteBulkAction::make()
                    ->before(static function (Collection $records): void {
                        $unreadNotifications = DatabaseNotification::whereIn('data->viewData->inquiry_id', $records->pluck('id')->toArray())->get();

                        foreach ($unreadNotifications as $unreadNotification) {
                            $unreadNotification->delete();
                        }

                        $notifications = DatabaseNotification::where('notifiable_id', auth()->user()->id)
                            ->whereNull('read_at')
                            ->count();

                        event(new UpdateNotificationBadgeCountEvent(auth()->user(), $notifications));
                    }),
                \App\Filament\Tables\Actions\RestoreBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInquiries::route('/'),
            'create' => Pages\CreateInquiry::route('/create'),
            'view' => Pages\ViewInquiry::route('/{record}'),
            'edit' => Pages\EditInquiry::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        if (!auth()->user()->hasRole('super-admin')) {
            return parent::getEloquentQuery()->where('deleted_at', null);
        }

        return parent::getEloquentQuery()
            ->withoutGlobalScopes(array(SoftDeletingScope::class));
    }

    protected static function getNavigationBadge(): ?string
    {
        return DatabaseNotification::where('notifiable_id', auth()->user()->id)->whereNull('read_at')->count();
    }

    protected static function getNavigationBadgeColor(): ?string
    {
        return 'danger';
    }

    public static function tooltip()
    {
        return function (TextColumn $column): ?string {
            $state = $column->getState();

            if (strlen($state) <= $column->getLimit()) {
                return null;
            }

            return $state;
        };
    }
}
