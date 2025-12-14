<?php

namespace App\Filament\Resources\Facilities\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;

use App\Models\City;
use App\Models\FacilityType;

class FacilitiesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('user.profile_image_url')
                    ->label(__('panel.profileImage'))
                    ->label(false)
                    ->disk('public')
                    ->circular()
                    ->height(40)
                    ->width(40)
                    ->defaultImageUrl(fn ($record) => 'https://ui-avatars.com/api/?name=' . urlencode($record->user->name) . '&background=random&length=2')
                    ->size(40),
                TextColumn::make('user.name')
                    ->label(__('panel.user'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('user.email')
                    ->label(__('panel.email')),
                TextColumn::make('user.phone')
                    ->label(__('panel.phone'))
                    ->formatStateUsing(fn ($record) => $record->user->country_code . $record->user->number),
                TextColumn::make('city.name')
                    ->label(__('panel.city')),
                TextColumn::make('type.name')
                    ->badge()
                    ->label(__('panel.facilityType')),
            ])
            ->filters([
                SelectFilter::make('city_id')
                    ->label(__('panel.city'))
                    ->options(City::all()->pluck('name', 'id')),
                SelectFilter::make('type_id')
                    ->label(__('panel.facilityType'))
                    ->options(FacilityType::all()->pluck('name', 'id')),
            ])
            ->recordActions([
                EditAction::make()->icon('heroicon-m-pencil')->label(false)->tooltip(__('panel.edit')),
                DeleteAction::make()->icon('heroicon-m-trash')->label(false)->tooltip(__('panel.delete')),
            ])
            ->toolbarActions([

            ]);
    }
}
