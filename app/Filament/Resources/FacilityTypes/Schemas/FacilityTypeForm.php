<?php

namespace App\Filament\Resources\FacilityTypes\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;

class FacilityTypeForm
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
            ]);
    }
}
