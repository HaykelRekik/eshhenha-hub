<?php

declare(strict_types=1);

namespace App\Providers;

use App\Enums\Icons\PhosphorIcons;
use BezhanSalleh\FilamentLanguageSwitch\LanguageSwitch;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Wizard;
use Filament\Tables\Table;
use Illuminate\Support\ServiceProvider;

class UtilityServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        LanguageSwitch::configureUsing(modifyUsing: function (LanguageSwitch $switch): void {
            $switch
                ->locales(locales: config()->array('app.supported_locales'))
                ->circular();
        });

        CreateAction::configureUsing(
            modifyUsing: fn (CreateAction $createAction): CreateAction => $createAction->createAnother(condition: false)
                ->icon(icon: PhosphorIcons::PlusCircleDuotone)
        );

        Table::configureUsing(modifyUsing: fn (Table $table): Table => $table->emptyStateHeading(heading: __('No records found.'))
            ->paginationPageOptions(options: [5, 10])
            ->defaultPaginationPageOption(option: 10)
            ->defaultDateTimeDisplayFormat(format: 'd M Y , h:i A'));

        Select::configureUsing(modifyUsing: fn (Select $select): Select => $select->native(false));

        Section::configureUsing(modifyUsing: fn (Section $section): Section => $section->columns(2)->columnSpanFull());

        Tab::configureUsing(modifyUsing: fn (Tab $tab): Tab => $tab->columnSpanFull());

        Wizard::configureUsing(modifyUsing: fn (Wizard $wizard): Wizard => $wizard->columnSpanFull()->skippable(app()->isLocal()));

        ToggleButtons::configureUsing(modifyUsing: fn (ToggleButtons $toggleButtons): ToggleButtons => $toggleButtons->inline());

    }
}
