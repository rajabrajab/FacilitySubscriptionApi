<?php

namespace App\Filament\Resources\FacilityTypes\Tables;

use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class FacilityTypesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('panel.name'))
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([

            ])
            ->headerActions([
            ])
            ->recordActions([
                EditAction::make()->icon('heroicon-m-pencil')->label(false)->tooltip(__('panel.edit')),
                DeleteAction::make()->icon('heroicon-m-trash')->label(false)->tooltip(__('panel.delete')),
            ]);
    }
}
