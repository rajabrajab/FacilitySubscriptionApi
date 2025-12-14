<?php

namespace App\Filament\Resources\FacilityTypes;

use App\Filament\Resources\FacilityTypes\Pages\ListFacilityTypes;
use App\Filament\Resources\FacilityTypes\Schemas\FacilityTypeForm;
use App\Filament\Resources\FacilityTypes\Schemas\FacilityTypeInfolist;
use App\Filament\Resources\FacilityTypes\Tables\FacilityTypesTable;
use App\Models\FacilityType;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class FacilityTypeResource extends Resource
{
    protected static ?string $model = FacilityType::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Tag;

    public static function getNavigationLabel(): string
    {
        return __('panel.facilityTypes');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('panel.facilityManagement');
    }

    public static function getNavigationSort(): ?int
    {
        return 2;
    }

    public static function getTitle(): string
    {
        return __('panel.facilityTypes');
    }

    public static function getModelLabel(): string
    {
        return __('panel.facilityType');
    }

    public static function getPluralModelLabel(): string
    {
        return __('panel.facilityTypes');
    }

    protected static ?string $recordTitleAttribute = 'no';

    public static function form(Schema $schema): Schema
    {
        return FacilityTypeForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return FacilityTypeInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return FacilityTypesTable::configure($table);
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
            'index' => ListFacilityTypes::route('/'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
