<?php

declare(strict_types=1);

namespace App\Filament\Pages\Auth;

use Filament\Auth\Pages\Register as BaseRegister;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Width;

class Register extends BaseRegister
{
    protected Width|string|null $maxContentWidth = Width::TwoExtraLarge;

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                $this->getRoleFormComponent(),
                $this->getNameFormComponent(),
                $this->getEmailFormComponent(),
                $this->getPasswordFormComponent(),
                $this->getPasswordConfirmationFormComponent(),
                $this->getVATNumberFormComponent(),
                $this->getCRNumberFormComponent(),
            ]);
    }

    protected function getRoleFormComponent(): Component
    {
        return ToggleButtons::make('role')
            ->label(__('Register as'))
            ->inline()
            ->options([
                'customer' => __('Customer'),
                'company' => __('Company'),
            ]);
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
        return TextInput::make('cr_number')
            ->label(__('CR Number'))
            ->required(fn (Get $get): bool => 'company' === $get('role'))
            ->visibleJs(
                <<<'JS'
                    $get('role') === 'company'
                JS
            );
    }
}
