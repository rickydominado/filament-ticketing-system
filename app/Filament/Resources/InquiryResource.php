<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InquiryResource\Pages;
use App\Filament\Resources\InquiryResource\RelationManagers;
use App\Models\Inquiry;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
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
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
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
                                    ->required()
                                    ->minLength(8)
                                    ->maxLength(50),

                                TextInput::make('email')
                                    ->autofocus()
                                    ->placeholder('Your Email')
                                    ->disableAutocomplete()
                                    ->required()
                                    ->email()
                                    ->minLength(8)
                                    ->maxLength(50),
                            ]),

                        Fieldset::make('Inquiry Information')
                            ->schema([
                                TextInput::make('title')
                                    ->autofocus()
                                    ->placeholder('Inquiry Title')
                                    ->disableAutocomplete()
                                    ->required()
                                    ->minLength(8)
                                    ->maxLength(50),

                                Grid::make(3)
                                    ->schema([
                                        Select::make('category_id')
                                            ->autofocus()
                                            ->placeholder('Select a category')
                                            ->relationship('category', 'name')
                                            ->required(),
                                    ]),

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

                                Textarea::make('content')
                                    ->autofocus()
                                    ->placeholder('Inquiry Content...')
                                    ->required()
                                    ->minLength(10)
                                    ->maxLength(255),

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
                                            ->imagePreviewHeight('80')
                                            ->removeUploadedFileButtonPosition('right')
                                            ->multiple()
                                            ->maxFiles(5)
                                            ->image()
                                            ->maxSize(1024)
                                            ->hint('max-images : 5 (imgs) | max-image-size : 1 (mb)')
                                            ->hintIcon('heroicon-s-exclamation-circle'),
                                    ])
                            ])
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
                            ->weight('bold')
                            ->alignment('right'),

                        TextColumn::make('content')
                            ->limit(15)
                            ->tooltip(self::tooltip())
                            ->alignment('right'),
                    ]),

                    TextColumn::make('category.name')
                        ->alignment('center'),

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
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
                Tables\Actions\ForceDeleteBulkAction::make(),
                Tables\Actions\RestoreBulkAction::make(),
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
            // 'view' => Pages\ViewInquiry::route('/{record}'),
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
