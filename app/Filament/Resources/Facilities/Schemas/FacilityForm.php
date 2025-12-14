<?php

namespace App\Filament\Resources\Facilities\Schemas;

use App\Models\City;
use App\Models\FacilityType;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ViewField;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class FacilityForm
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
                                    ->rules([
                                        function ($record) {
                                            return function (string $attribute, $value, \Closure $fail) use ($record) {
                                                $user = $record?->user;
                                                $exists = User::where('email', $value)
                                                    ->when($user, fn ($query) => $query->where('id', '!=', $user->id))
                                                    ->exists();
                                                if ($exists) {
                                                    $fail(__('validation.unique', ['attribute' => $attribute]));
                                                }
                                            };
                                        },
                                    ]),

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
                                                $livewire->js($js);
                                            })
                                    ),

                                TextInput::make('password_confirmation')
                                    ->label(__('panel.password_confirmation'))
                                    ->password()
                                    ->required(fn (string $context): bool => $context === 'create')
                                    ->dehydrated(false)
                                    ->minLength(8)
                                    ->maxLength(255)
                                    ->same('password'),

                                Select::make('facility_type_id')
                                    ->label(__('panel.facilityType'))
                                    ->relationship('type', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->columnSpanFull(),

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
                                    ->columnSpan(1)
                                    ->extraInputAttributes([
                                        'inputmode' => 'numeric',
                                        'pattern'   => '[0-9]*',
                                    ]),

                                Hidden::make('iso_code')
                                    ->default('SYR'),

                                Hidden::make('type')
                                    ->default('facility'),

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
                                        'facility_' . uniqid() . '.' . $file->getClientOriginalExtension()
                                )
                                ->required(fn (string $context): bool => $context === 'create')
                                ->columnSpanFull(),
                            ]),
                    ]),
                Section::make(__('panel.location_information'))
                    ->description(__('panel.location_information_description'))
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('address_name')
                                    ->label(__('panel.addressName'))
                                    ->required()
                                    ->maxLength(255)
                                    ->columnSpanFull(),
                                Select::make('city_id')
                                    ->label(__('panel.city'))
                                    ->relationship('city', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required(),

                                TextInput::make('street')
                                    ->label(__('panel.street'))
                                    ->maxLength(255),

                                TextInput::make('building')
                                    ->label(__('panel.building'))
                                    ->maxLength(255),

                                TextInput::make('floor')
                                    ->label(__('panel.floor'))
                                    ->maxLength(255),

                                TextInput::make('apartment')
                                    ->label(__('panel.apartment'))
                                    ->maxLength(255)
                                    ->columnSpanFull(),

                                TextInput::make('latitude')
                                    ->label(__('panel.latitude'))
                                    ->numeric()
                                    ->step(0.000001)
                                    ->default(fn ($record) => $record?->coordinates['lat'] ?? null)
                                    ->dehydrated(false)
                                    ->live()
                                    ->afterStateUpdated(function ($state, $set, $get) {
                                        $lat = $get('latitude');
                                        $lng = $get('longitude');
                                        if ($lat !== null && $lng !== null && $lat != 0 && $lng != 0) {
                                            $set('coordinates', ['lat' => (float)$lat, 'lng' => (float)$lng]);
                                        }
                                    }),

                                TextInput::make('longitude')
                                    ->label(__('panel.longitude'))
                                    ->numeric()
                                    ->step(0.000001)
                                    ->default(fn ($record) => $record?->coordinates['lng'] ?? null)
                                    ->dehydrated(false)
                                    ->live()
                                    ->afterStateUpdated(function ($state, $set, $get) {
                                        $lat = $get('latitude');
                                        $lng = $get('longitude');
                                        if ($lat !== null && $lng !== null && $lat != 0 && $lng != 0) {
                                            $set('coordinates', ['lat' => (float)$lat, 'lng' => (float)$lng]);
                                        }
                                    }),

                                Hidden::make('coordinates')
                                    ->dehydrateStateUsing(function ($get) {
                                        $lat = $get('latitude');
                                        $lng = $get('longitude');
                                        if ($lat && $lng && $lat != 0 && $lng != 0) {
                                            return ['lat' => (float)$lat, 'lng' => (float)$lng];
                                        }
                                        return null;
                                    })
                                    ->default(fn ($record) => $record?->coordinates ?? null),
                            ]),
                    ]),
            ]);
    }
}
