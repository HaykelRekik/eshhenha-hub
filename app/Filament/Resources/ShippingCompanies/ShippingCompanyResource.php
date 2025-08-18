<?php

declare(strict_types=1);

namespace App\Filament\Resources\ShippingCompanies;

use App\Filament\Resources\ShippingCompanies\Pages\CreateShippingCompany;
use App\Filament\Resources\ShippingCompanies\Pages\EditShippingCompany;
use App\Filament\Resources\ShippingCompanies\Pages\ListShippingCompanies;
use App\Filament\Resources\ShippingCompanies\Pages\ViewShippingCompany;
use App\Filament\Resources\ShippingCompanies\Schemas\ShippingCompanyForm;
use App\Filament\Resources\ShippingCompanies\Schemas\ShippingCompanyInfolist;
use App\Filament\Resources\ShippingCompanies\Tables\ShippingCompaniesTable;
use App\Models\ShippingCompany;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class ShippingCompanyResource extends Resource
{
    protected static ?string $model = ShippingCompany::class;

    protected static string|BackedEnum|null $navigationIcon = null;

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return ShippingCompanyForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ShippingCompanyInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ShippingCompaniesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [

        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListShippingCompanies::route('/'),
            'create' => CreateShippingCompany::route('/create'),
            'view' => ViewShippingCompany::route('/{record}'),
            'edit' => EditShippingCompany::route('/{record}/edit'),
        ];
    }

    public static function getLabel(): ?string
    {
        return __('Shipping Company');
    }

    public static function getPluralLabel(): ?string
    {
        return __('Shipping Companies');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Shipments Management');
    }
}
