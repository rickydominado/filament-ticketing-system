<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\Pages\CreateUser;
use App\Filament\Resources\UserResource\Pages\EditUser;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Carbon\Carbon;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Page;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Card::make()
                    ->schema([
                        Fieldset::make('User Information')
                            ->schema([
                                TextInput::make('firstname')
                                    ->autofocus()
                                    ->placeholder('Firstname')
                                    ->disableAutocomplete()
                                    ->required()
                                    ->minLength(3)
                                    ->maxLength(50),

                                TextInput::make('lastname')
                                    ->autofocus()
                                    ->placeholder('Lastname')
                                    ->disableAutocomplete()
                                    ->required()
                                    ->minLength(3)
                                    ->maxLength(50),

                                Grid::make(2)
                                    ->schema([
                                        TextInput::make('email')
                                            ->email()
                                            ->unique(User::class, ignoreRecord: true)
                                            ->autofocus()
                                            ->placeholder('Email')
                                            ->disableAutocomplete()
                                            ->minLength(8)
                                            ->maxLength(50),

                                        TextInput::make('address')
                                            ->autofocus()
                                            ->placeholder('Address')
                                            ->disableAutocomplete()
                                            ->minLength(20)
                                            ->maxLength(50),
                                    ]),

                                Grid::make(3)
                                    ->schema([
                                        TextInput::make('mobile_number')
                                            ->label('Mobile number')
                                            ->tel()
                                            ->autofocus()
                                            ->placeholder('+63(000)000-00-00')
                                            ->disableAutocomplete()
                                            ->mask(fn (TextInput\Mask $mask) => $mask->pattern('+{63}(000)000-00-00')),
                                    ])
                            ]),

                        Fieldset::make('Account Information')
                            ->schema([
                                TextInput::make('username')
                                    ->autofocus()
                                    ->placeholder('Username')
                                    ->disableAutocomplete()
                                    ->required()
                                    ->minLength(8)
                                    ->maxLength(20),

                                TextInput::make('password')
                                    ->password()
                                    ->label(fn (Page $livewire): string => ($livewire instanceof EditUser) ? 'New Password' : 'Password')
                                    ->dehydrateStateUsing(fn (null|string $state): null|string => filled($state) ? Hash::make($state) : null)
                                    ->dehydrated(fn (null|string $state): bool => filled($state))
                                    ->required(fn (Page $livewire) => ($livewire instanceof CreateUser))
                                    ->autofocus()
                                    ->placeholder('Password')
                                    ->disableAutocomplete()
                                    ->minLength(8)
                                    ->maxLength(20),

                                Grid::make(3)
                                    ->schema([
                                        Select::make('role')
                                            ->options(Role::pluck('name', 'id'))
                                            ->autofocus()
                                            ->placeholder('Select a role')
                                            ->disablePlaceholderSelection()
                                            ->default(3)
                                            ->reactive()
                                            ->afterStateUpdated(function (callable $get, callable $set) {
                                                Role::find($get('role'))->name !== 'agent' ? $set('is_admin', true) : $set('is_admin', false);
                                            })
                                            ->required()
                                            ->disabled(!auth()->user()->hasRole('super-admin')),

                                        Hidden::make('is_admin')
                                            ->default(false),
                                    ]),
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
                        ->grow(false),

                    IconColumn::make('is_admin')
                        ->boolean()
                        ->trueIcon('heroicon-o-user')
                        ->falseIcon('heroicon-o-user')
                        ->falseColor('primary')
                        ->grow(false),

                    TextColumn::make('firstname')
                        ->searchable()
                        ->limit(10)
                        ->tooltip(self::tooltip())
                        ->grow(false),

                    TextColumn::make('lastname')
                        ->searchable()
                        ->limit(10)
                        ->tooltip(self::tooltip()),

                    Stack::make([
                        TextColumn::make('address')
                            ->searchable()
                            ->limit(25)
                            ->tooltip(self::tooltip())
                            ->weight('bold')
                            ->alignment('right'),

                        TextColumn::make('mobile_number')
                            ->formatStateUsing(fn (?string $state): ?string => preg_replace('/^(\d{2})(\d{3})(\d{3})(\d{2})(\d{2})$/', '+$1($2)$3-$4-$5', $state))
                            ->searchable()
                            ->alignment('right')
                    ]),

                    Stack::make([
                        TextColumn::make('username')
                            ->searchable()
                            ->limit(25)
                            ->tooltip(self::tooltip())
                            ->weight('bold')
                            ->alignment('center'),

                        TextColumn::make('email')
                            ->searchable()
                            ->limit(20)
                            ->tooltip(self::tooltip())
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
                            ->color('warning'),
                    ])->grow(false),
                ])
            ])
            ->defaultSort('created_at')
            ->filters([
                SelectFilter::make('role')
                    ->relationship('roles', 'name')
                    ->indicator('Role')
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

                TrashedFilter::make()
                    ->visible(auth()->user()->hasRole('super-admin')),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        if (!auth()->user()->hasRole('super-admin')) {
            return parent::getEloquentQuery()->where('is_admin', false);
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
