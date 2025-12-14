<?php

namespace App\Filament\Resources\Packages\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;

class PackagesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('panel.name'))
                    ->sortable()
                    ->searchable(),
                TextColumn::make('max_usages')
                    ->label(__('panel.maxUsages'))
                    ->badge(),
                TextColumn::make('days')
                    ->label(__('panel.days'))
                    ->badge(),
                TextColumn::make('requires_approval')
                    ->formatStateUsing(fn ($state) => $state ? __('panel.yes') : __('panel.no'))
                    ->label(__('panel.requiresApproval'))
                    ->badge()
                    ->color(fn ($state) => $state ? 'success' : 'danger'),
                TextColumn::make('targetGroup.name')
                    ->label(__('panel.targetGroup')),
                TextColumn::make('facilityTypes.name')
                    ->label(__('panel.facilityTypes')),
            ])
            ->filters([

            ])
            ->recordActions([
                EditAction::make()->icon('heroicon-m-pencil')->label(false)->tooltip(__('panel.edit')),
                DeleteAction::make()->icon('heroicon-m-trash')->label(false)->tooltip(__('panel.delete')),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
