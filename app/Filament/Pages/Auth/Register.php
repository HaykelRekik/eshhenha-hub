<?php

declare(strict_types=1);

namespace App\Filament\Pages\Auth;

use App\Enums\Icons\PhosphorIcons;
use App\Filament\Support\Components\AddressBloc;
use App\Models\Address;
use App\Models\City;
use App\Models\Company;
use App\Models\Country;
use App\Models\Region;
use App\Models\User;
use App\Rules\SaudiNationalID;
use Filament\Auth\Pages\Register as BaseRegister;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Width;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;

class Register extends BaseRegister
{
    protected Width|string|null $maxContentWidth = Width::ThreeExtraLarge;

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Wizard::make()
                    ->contained(false)
                    ->components([
                        Step::make('Account Details')
                            ->icon(PhosphorIcons::UserDuotone)
                            ->columns(3)
                            ->components([
                                ToggleButtons::make('role')
                                    ->label(__('Register as'))
                                    ->grouped()
                                    ->options([
                                        'customer' => __('customer'),
                                        'company' => __('company'),
                                    ])
                                    ->icons([
                                        'customer' => PhosphorIcons::UserDuotone,
                                        'company' => PhosphorIcons::BuildingOfficeDuotone,
                                    ])
                                    ->required()
                                    ->live(),

                                TextInput::make('name')
                                    ->label(__('Name'))
                                    ->required(),

                                TextInput::make('email')
                                    ->label('Email')
                                    ->email()
                                    ->required()
                                    ->unique('users', 'email'),

                                TextInput::make('password')
                                    ->label(__('Password'))
                                    ->password()
                                    ->required()
                                    ->same('passwordConfirmation'),

                                TextInput::make('passwordConfirmation')
                                    ->password()
                                    ->required()
                                    ->label('Password Confirmation')
                                    ->dehydrated(false),

                                PhoneInput::make('phone_number')
                                    ->label(__('Phone Number'))
                                    ->required()
                                    ->unique('users', 'phone_number'),


                            ]),

                        Step::make('Additional Information')
                            ->icon('heroicon-o-clipboard-document-list')
                            ->schema(function ($get): array {
                                if ('customer' === $get('role')) {
                                    return [
                                        AddressBloc::make()
                                    ];
                                }

                                return [
                                    Fieldset::make('Company Information')
                                        ->schema([
                                            TextInput::make('company_name')
                                                ->required()
                                                ->label(__('Company Name')),

                                            TextInput::make('cr_number')
                                                ->required()
                                                ->unique(Company::class)
                                                ->label('Commercial Registration Number'),

                                            TextInput::make('vat_number')
                                                ->required()
                                                ->unique(Company::class),

                                            TextInput::make('company_phone')
                                                ->tel()
                                                ->required(),
                                        ]),

                                    Fieldset::make('Bank Information')
                                        ->schema([
                                            TextInput::make('iban')
                                                ->required()
                                                ->maxLength(255),

                                            TextInput::make('swift')
                                                ->required()
                                                ->maxLength(255),

                                            TextInput::make('bank_code')
                                                ->required()
                                                ->maxLength(255),

                                            TextInput::make('bank_account_number')
                                                ->required()
                                                ->maxLength(255),
                                        ])
                                        ->columns(2),
                                ];
                            }),
                    ])
                    ->submitAction(view('filament.components.register-submit')),
            ]);
    }

    protected function handleRegistration(array $data): Model
    {
        dd($data);
        return DB::transaction(fn() => tap(User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
            'phone_number' => $data['phone_number'],
            'national_id' => 'customer' === $data['role'] ? $data['national_id'] : null,
        ]), function (User $user) use ($data): void {
            if ('customer' === $data['role']) {
                $user->assignRole('customer');
                Address::create([
                    'user_id' => $user->id,
                    'country_id' => $data['country_id'],
                    'region_id' => $data['region_id'],
                    'city_id' => $data['city_id'],
                    'street' => $data['street'],
                    'zip_code' => $data['zip_code'],
                ]);
            }

            if ('company' === $data['role']) {
                $user->assignRole('company');

                $user->company()->create([
                    'name' => $data['company_name'],
                    'cr_number' => $data['cr_number'],
                    'vat_number' => $data['vat_number'],
                    'phone_number' => $data['company_phone'],
                    'iban' => $data['iban'],
                    'swift' => $data['swift'],
                    'bank_code' => $data['bank_code'],
                    'bank_account_number' => $data['bank_account_number'],
                ]);
            }
        }));
    }

    protected function getFormActions(): array
    {
        return [];
    }
}
