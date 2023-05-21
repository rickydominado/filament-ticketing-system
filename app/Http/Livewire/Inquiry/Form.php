<?php

namespace App\Http\Livewire\Inquiry;

use App\Models\Inquiry;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Illuminate\Contracts\View\View;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\URL;
use Livewire\Component;
use Livewire\TemporaryUploadedFile;

class Form extends Component implements HasForms
{
    use InteractsWithForms;

    // User Information Fieldset
    public $name;
    public $email;

    // Inquiry Information Fieldset
    public $title;
    public $category_id;
    public $status;
    public $severity;
    public $content;
    public $image;

    protected function getFormModel(): string
    {
        return Inquiry::class;
    }

    protected function getFormSchema(): array
    {
        return [
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
                    Grid::make(1)
                        ->schema([
                            TextInput::make('title')
                                ->autofocus()
                                ->placeholder('Inquiry Title')
                                ->disableAutocomplete()
                                ->required()
                                ->minLength(8)
                                ->maxLength(50),
                        ]),

                    Grid::make(2)
                        ->schema([
                            Select::make('category_id')
                                ->autofocus()
                                ->placeholder('Select a category')
                                ->relationship('category', 'name')
                                ->required(),
                        ]),

                    Grid::make(1)
                        ->schema([
                            Textarea::make('content')
                                ->autofocus()
                                ->placeholder('Inquiry Content...')
                                ->required()
                                ->minLength(10)
                                ->maxLength(255),
                        ]),

                    Grid::make(1)
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
                                ->hintIcon('heroicon-s-exclamation-circle'),
                        ]),
                ]),
        ];
    }

    public function store(): Redirector
    {
        $inquiry = Inquiry::create($this->form->getState());

        $this->form->model($inquiry)->saveRelationships();

        $create_success_temp_url = URL::temporarySignedRoute('inquiry.create-success', now()->addSeconds(30), ['inquiry' => $inquiry]);

        return redirect($create_success_temp_url);
    }

    public function render(): View
    {
        return view('livewire.inquiry.form');
    }
}
