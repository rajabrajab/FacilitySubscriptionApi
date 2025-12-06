<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Actions\Action;
use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Filament\Forms\Components\Select;

class UserForm
{
     public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('panel.basic_information'))
                    ->description(__('panel.basic_information_description'))
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('name')
                                    ->label(__('panel.name'))
                                    ->required()
                                    ->maxLength(255),

                                TextInput::make('email')
                                    ->label(__('panel.email'))
                                    ->email()
                                    ->required()
                                    ->maxLength(255)
                                    ->unique(ignoreRecord: true),


                                TextInput::make('password')
                                    ->label(__('panel.password'))
                                    ->password()
                                    ->required(fn (string $context): bool => $context === 'create')
                                    ->minLength(8)
                                    ->maxLength(255)
                                    ->same('password_confirmation')
                                    ->extraAttributes([
                                        'x-ref' => 'passwordInput',
                                    ])
                                   ->suffixAction(
                                        Action::make('copy_password')
                                            ->icon('heroicon-m-clipboard')
                                            ->tooltip(__('panel.copy_password'))
                                            ->action(function ($livewire, $state) {
                                                $js = 'window.navigator.clipboard.writeText(' . json_encode((string) $state) . ');';
                                                $js .= '$tooltip("'.__('panel.password_copied').'", { timeout: 1500 });';
                                                $livewire->js($js);})
                                    ),

                                TextInput::make('password_confirmation')
                                    ->label(__('panel.password_confirmation'))
                                    ->password()
                                    ->required(fn (string $context): bool => $context === 'create')
                                    ->dehydrated(false)
                                    ->minLength(8)
                                    ->maxLength(255)
                                    ->same('password'),

                                Select::make('type')
                                    ->label(__('panel.userType'))
                                    ->options([
                                        'user' => __('panel.user'),
                                        'facility' => __('panel.facility'),
                                    ])
                                    ->required()
                                    ->columnSpanFull(),

                                    FileUpload::make('profile_image')
                                    ->image()
                                    ->label(__('panel.profileImage'))
                                    ->directory('profile-images')
                                    ->disk('public')
                                    ->visibility('public')
                                    ->preserveFilenames()
                                    ->imageEditor()
                                    ->maxSize(2048)
                                    ->getUploadedFileNameForStorageUsing(
                                        fn (TemporaryUploadedFile $file): string =>
                                            'user_' . uniqid() . '.' . $file->getClientOriginalExtension()
                                    )
                                    ->required()
                                    ->columnSpanFull(),
                            ]),
                    ]),

                Section::make(__('panel.contact_information'))
                    ->description(__('panel.contact_information_description'))
                    ->schema([
                        Grid::make(3)
                            ->schema([

                                TextInput::make('country_code')
                                    ->label(__('panel.country_code'))
                                    ->default('+963')
                                    ->disabled()
                                    ->dehydrated()
                                    ->required()
                                    ->columnSpan(1)
                                    ->extraInputAttributes([
                                        'class' => 'text-xs w-20',
                                    ]),

                                TextInput::make('number')
                                    ->label(__('panel.number'))
                                    ->tel()
                                    ->placeholder('9XXXXXXXX')
                                    ->required()
                                    ->rules([
                                        'regex:/^9[0-9]{8}$/',
                                    ])
                                    ->helperText(__('panel.phone_must_start_with_9'))
                                    ->columnSpan(2)
                                    ->extraInputAttributes([
                                        'inputmode' => 'numeric',
                                        'pattern'   => '[0-9]*',
                                    ]),

                                Hidden::make('iso_code')
                                    ->default('SYR'),
                            ]),
                    ]),
            ]);
    }
}
