<?php

namespace App\Filament\Resources\Packages\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Select;

class PackageForm
{
    public static function configure(Schema $schema): Schema
    {

    return $schema
        ->components([
            TextInput::make('name')
                ->label(__('panel.name'))
                ->required()
                ->columnSpanFull()
                ->maxLength(255),

            TextInput::make('max_usages')
                ->label(__('panel.maxUsages'))
                ->required()
                ->numeric()
                ->minValue(1)
                ->maxValue(1000),

            TextInput::make('days')
                ->label(__('panel.days'))
                ->required()
                ->numeric()
                ->minValue(1)
                ->maxValue(365),

            Select::make('target_group_id')
                ->label(__('panel.targetGroup'))
                ->relationship('targetGroup', 'name')
                ->searchable()
                ->preload()
                ->required(),

            Select::make('facilityTypes')
                ->label(__('panel.facilityTypes'))
                ->relationship('facilityTypes', 'name')
                ->multiple()
                ->searchable()
                ->preload(),

            Toggle::make('requires_approval')
                ->label(__('panel.requiresApproval'))
                ->required(),
        ]);
    }
}
