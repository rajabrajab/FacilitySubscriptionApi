<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Schemas\Schema;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;

use Filament\Infolists\Components\RepeatableEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;

class UserInfolist
{
      public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Tabs::make('UserTabs')
                    ->columnSpanFull()
                    ->tabs([

                        Tab::make(__('panel.basicInfo'))
                            ->schema([
                                Grid::make(2)
                                    ->schema([
                                    ImageEntry::make('profile_image_url')
                                    ->label(__('panel.profileImage'))
                                    ->disk('public')
                                    ->circular()
                                    ->hiddenLabel()
                                    ->height(150)
                                    ->width(150)
                                    ->defaultImageUrl(fn ($record) =>
                                        'https://ui-avatars.com/api/?name='
                                        . urlencode($record->name)
                                        . '&background=random&length=2'
                                    ),

                                    TextEntry::make('number')
                                    ->label(__('panel.number'))
                                    ->formatStateUsing(fn ($record) =>
                                        $record->country_code . $record->number
                                    ),
                                    TextEntry::make('name')
                                    ->label(__('panel.name')),
                                    TextEntry::make('email')
                                    ->label(__('panel.email')),

                                        TextEntry::make('type')
                                            ->label(__('panel.userType'))
                                            ->badge()
                                            ->color(fn (string $state): string => match ($state) {
                                                'user'     => 'secondary',
                                                'facility' => 'info',
                                                'admin'    => 'primary',
                                                default    => 'secondary',
                                            })
                                            ->formatStateUsing(fn (string $state) => __("panel.$state")),
                                    ]),
                                ]),
                        Tab::make(__('panel.subscriptions'))
                            ->schema([
                                RepeatableEntry::make('subscriptions')
                                    ->hiddenLabel()
                                    ->state(fn ($record) => $record->subscriptions)
                                    ->schema([
                                        Grid::make(2)
                                        ->schema([
                                        TextEntry::make('package.name')->label(__('panel.package')),
                                        TextEntry::make('status')->label(__('panel.status'))->badge()->color(fn (string $state) => match ($state) {
                                            'pending' => 'warning',
                                            'confirmed' => 'success',
                                            'cancelled' => 'danger',
                                            'expired' => 'gray',
                                            default => 'secondary',
                                            }),
                                            TextEntry::make('expire_at')->label(__('panel.expireAt'))->date(),
                                            TextEntry::make('used_times')->label(__('panel.usedTimes')),
                                        ]),
                                    ])
                                    ->visible(fn ($record) => $record->subscriptions->isNotEmpty()),

                                TextEntry::make('noSubscriptions')
                                    ->hiddenLabel()
                                    ->state(__('panel.noSubscriptionsDescription'))
                                    ->visible(fn ($record) => $record->subscriptions->isEmpty()),
                                    ]),
                                ]),
                ]);
    }
}
