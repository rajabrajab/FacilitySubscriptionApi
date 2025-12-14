<?php

namespace App\Filament\Resources\TargetGroups\Pages;

use App\Filament\Resources\TargetGroups\TargetGroupResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditTargetGroup extends EditRecord
{
    protected static string $resource = TargetGroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
