<?php

namespace App\Filament\Resources\Users\Tables;

use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('profile_image_url')
                    ->label(false)
                    ->disk('public')
                    ->circular()
                    ->height(40)
                    ->width(40)
                    ->defaultImageUrl(fn ($record) => 'https://ui-avatars.com/api/?name=' . urlencode($record->name) . '&background=random&length=2')
                    ->size(40),

                TextColumn::make('name')
                    ->label(__('panel.name'))
                    ->searchable(),

                TextColumn::make('number')
                    ->label(__('panel.number'))
                    ->formatStateUsing(fn ($record) => $record->country_code . $record->number)
                    ->searchable(),

                TextColumn::make('email')
                    ->label(__('panel.email'))
                    ->searchable(),

              TextColumn::make('type')
                ->label(__('panel.userType'))
                ->badge()
                ->color(fn (string $state): string => match ($state) {
                    'user' => 'secondary',
                    'facility' => 'info',
                    'admin' => 'primary',
                    default => 'secondary',
                })
                ->formatStateUsing(fn (string $state): string => __("panel.$state"))
                ->searchable(),
            ])
            ->filters([
            ])
            ->recordActions([
                ViewAction::make()->icon('heroicon-m-eye')->label(false)->tooltip(__('panel.view'))->modalHeading(__('panel.userDetails')),
                DeleteAction::make()->icon('heroicon-m-trash')->label(false)->tooltip(__('panel.delete'))->modalHeading(__('panel.deleteUser')),
            ]);
    }
}
