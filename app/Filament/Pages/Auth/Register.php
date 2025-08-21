<?php

declare(strict_types=1);

namespace App\Filament\Pages\Auth;

use App\Filament\Support\Components\BankDetailsBloc;
use Filament\Auth\Pages\Register as BaseRegister;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Width;

class Register extends BaseRegister
{
    protected Width|string|null $maxContentWidth = Width::TwoExtraLarge;

    public function form(Schema $schema): Schema
    {
        return $schema
            ->columns(3)
            ->components([
                $this->getRoleFormComponent(),
                $this->getNameFormComponent(),
                $this->getEmailFormComponent(),

                Grid::make()
                    ->columns(2)
                    ->columnSpanFull()
                    ->components([
                        $this->getPasswordFormComponent(),
                        $this->getPasswordConfirmationFormComponent(),
                        $this->getVATNumberFormComponent(),
                        $this->getCRNumberFormComponent(),
                        $this->getBankDetailsFormComponent(),

                    ]),
            ]);
    }

    protected function getRoleFormComponent(): Component
    {
        return ToggleButtons::make('role')
            ->label(__('Register as'))
            ->inline()
            ->grouped()
            ->options([
                'customer' => __('customer'),
                'company' => __('company'),
            ])
            ->in(['customer', 'company']);
    }

    protected function getVATNumberFormComponent(): Component
    {
        return TextInput::make('vat_number')
            ->label(__('VAT Number'))
            ->required(fn (Get $get): bool => 'company' === $get('role'))
            ->visibleJs(
                <<<'JS'
                    $get('role') === 'company'
                JS
            );
    }

    protected function getCRNumberFormComponent(): Component
    {
        return TextInput::make('company.cr_number')
            ->label(__('CR Number'))
            ->required(fn (Get $get): bool => 'company' === $get('role'))
            ->visibleJs(
                <<<'JS'
                    $get('role') === 'company'
                JS
            );
    }

    private function getBankDetailsFormComponent(): Component
    {
        return Grid::make()
            ->columns(2)
            ->visibleJs(
                <<<'JS'
                    $get('role') === 'company'
                JS
            )
            ->components(BankDetailsBloc::make());
    }
}
