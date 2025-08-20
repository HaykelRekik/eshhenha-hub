<?php

declare(strict_types=1);

namespace App\Filament\Resources\Companies\Schemas;

use App\Enums\UserRole;

use App\Models\User;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Operation;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;

class CompanyForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->columns(3)
                    ->components([
                        Select::make('user_id')
                            ->label(__('User account'))
                            ->relationship(
                                name: 'user',
                                titleAttribute: 'name',
                                modifyQueryUsing: fn ($query) => $query->role(UserRole::COMPANY)->whereDoesntHave('company')
                            )
                            ->required()
                            ->searchable()
                            ->preload()
                            ->disabledOn(Operation::Edit->value)
                            ->createOptionForm(UserAccountForm::make())
                            ->createOptionUsing(fn (array $data): int => User::create([
                                ...$data,
                                'role' => UserRole::COMPANY,
                            ])
                                ->getKey()),

                        TextInput::make('name')
                            ->label(__('Company Name'))
                            ->required(),
                        TextInput::make('email')
                            ->label(__('Contact Email'))
                            ->required()
                            ->email()
                            ->unique(ignoreRecord: true),

                        PhoneInput::make('phone_number')
                            ->label(__('Contact Phone Number'))
                            ->required()
                            ->unique(ignoreRecord: true),

                        Select::make('shipping_companies')
                            ->label(__('Contracts'))
                            ->relationship(
                                name: 'contracts',
                                titleAttribute: 'name'
                            )
                            ->searchable()
                            ->multiple()
                            ->hint(__('Shipping Companies'))
                            ->preload(),

                        ToggleButtons::make('is_active')
                            ->label(__('Status'))
                            ->default(true)
                            ->boolean(
                                trueLabel: __('Active'),
                                falseLabel: __('Inactive'),
                            )
                            ->required(),

                        FileUpload::make('logo')
                            ->label(__('Company Logo'))
                            ->columnSpanFull()
                            ->nullable()
                            ->image()
                            ->directory('companies')
                            ->maxSize(1024),
                    ]),
            ]);
    }
}
