<?php

declare(strict_types=1);

namespace App\Providers;

use App\Enums\Icons\PhosphorIcons;

use BezhanSalleh\LanguageSwitch\LanguageSwitch;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Infolists\Components\ImageEntry;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Wizard;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Table;
use Illuminate\Support\ServiceProvider;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;

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

        Wizard::configureUsing(modifyUsing: fn (Wizard $wizard): Wizard => $wizard->columnSpanFull()->skippable(app()->isLocal()));

        ToggleButtons::configureUsing(modifyUsing: fn (ToggleButtons $toggleButtons): ToggleButtons => $toggleButtons->inline());

        TextInput::configureUsing(modifyUsing: fn (TextInput $input): TextInput => $input->maxLength(255));

        ImageColumn::configureUsing(modifyUsing: fn (ImageColumn $column): ImageColumn => $column->extraImgAttributes(['loading' => 'lazy']));

        PhoneInput::configureUsing(modifyUsing: fn (PhoneInput $phoneInput): PhoneInput => $phoneInput->strictMode(true)
            ->formatAsYouType()
            ->disableLookup()
            ->initialCountry('SA')
            ->excludeCountries(['IL'])
            ->autoPlaceholder('aggressive')
            ->defaultCountry('SA'));

        foreach ([
            Section::class,
            Grid::class,
            Tabs::class,
            Fieldset::class,
        ] as $component) {
            /** @var $component Section|Grid|Tabs|Fieldset */
            $component::configureUsing(
                modifyUsing: fn ($instance) => $instance->columns(2)->columnSpanFull()
            );
        }

        FileUpload::configureUsing(fn (FileUpload $fileUpload): FileUpload => $fileUpload
            ->visibility('public'));

        ImageColumn::configureUsing(fn (ImageColumn $imageColumn): ImageColumn => $imageColumn
            ->visibility('public'));

        ImageEntry::configureUsing(fn (ImageEntry $imageEntry): ImageEntry => $imageEntry
            ->visibility('public'));

    }
}
