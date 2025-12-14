<?php

namespace App\Filament\Resources\FacilityTypes\Schemas;

use Filament\Schemas\Schema;
use Filament\Infolists\Components\TextEntry;

class FacilityTypeInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name')
                    ->label(__('panel.name')),
            ]);
    }
}

