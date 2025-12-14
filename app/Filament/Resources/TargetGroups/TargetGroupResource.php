<?php

namespace App\Filament\Resources\TargetGroups;

use App\Filament\Resources\TargetGroups\Pages\CreateTargetGroup;
use App\Filament\Resources\TargetGroups\Pages\EditTargetGroup;
use App\Filament\Resources\TargetGroups\Pages\ListTargetGroups;
use App\Filament\Resources\TargetGroups\Schemas\TargetGroupForm;
use App\Filament\Resources\TargetGroups\Tables\TargetGroupsTable;
use App\Models\TargetGroup;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class TargetGroupResource extends Resource
{
    protected static ?string $model = TargetGroup::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Users;

    public static function getNavigationLabel(): string
    {
        return __('panel.targetGroups');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('panel.facilityManagement');
    }

    public static function getNavigationSort(): ?int
    {
        return 4;
    }

    public static function getTitle(): string
    {
        return __('panel.targetGroups');
    }

    public static function getModelLabel(): string
    {
        return __('panel.targetGroup');
    }

    public static function getPluralModelLabel(): string
    {
        return __('panel.targetGroups');
    }

    public static function form(Schema $schema): Schema
    {
        return TargetGroupForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TargetGroupsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTargetGroups::route('/'),
        ];
    }
}
