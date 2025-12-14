<?php

namespace App\Filament\Resources\TargetGroups\Pages;

use App\Filament\Resources\TargetGroups\TargetGroupResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListTargetGroups extends ListRecords
{
    protected static string $resource = TargetGroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->createAnother(false),
        ];
    }
}
