<?php

declare(strict_types=1);

namespace App\Filament\Pages\User;

use App\DTOs\Payment\PaymentRequestDto;
use App\Enums\Icons\PhosphorIcons;
use App\Filament\Pages\User\Actions\TopupBalanceAction;
use App\Models\User;
use App\Models\WalletTransaction;
use App\Services\Payment\MyFatoorahService;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Concerns\InteractsWithInfolists;
use Filament\Infolists\Contracts\HasInfolists;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class WalletAndLoyaltyPage extends Page implements HasInfolists, HasTable
{
    use InteractsWithInfolists;
    use InteractsWithTable;

    protected static string|null|BackedEnum $navigationIcon = PhosphorIcons::WalletDuotone;

    //    protected static string $view = 'filament.pages.blank';

    protected static ?string $slug = 'user/wallet-and-loyalty';

    public function getTitle(): string|Htmlable
    {
        return __('Wallet & Loyalty');
    }

    protected function getHeaderActions(): array
    {
        return [
            TopupBalanceAction::make(),
        ];
    }

    //    public function table(Table $table): Table
    //    {
    //        return $table
    //            ->query($this->getUserTransactionsQuery())
    //            ->columns([
    //                TextColumn::make('created_at')
    //                    ->label(__('Date'))
    //                    ->dateTime('d M Y, h:i A')
    //                    ->sinceTooltip(),
    //
    //                TextColumn::make('amount')
    //                    ->label(__('Amount'))
    //                    ->saudiRiyal(),
    //
    //                BadgeColumn::make('type')
    //                    ->label(__('Type'))
    //                    ->colors([
    //                        'success' => fn($state) => in_array((string)$state, ['deposit', 'loyalty points conversion', 'transfer in', 'refund'], true),
    //                        'danger' => fn($state) => in_array((string)$state, ['withdrawal', 'transfer out', 'fee'], true),
    //                        'gray' => fn($state) => 'expiration' === (string)$state,
    //                        'warning' => fn($state) => 'adjustment' === (string)$state,
    //                    ])
    //                    ->formatStateUsing(fn($state) => __((string)$state)),
    //
    //                TextColumn::make('balance_after')
    //                    ->label(__('Balance After'))
    //                    ->saudiRiyal(),
    //
    //                TextColumn::make('metadata.reason')
    //                    ->label(__('Reason'))
    //                    ->wrap(),
    //            ])
    //            ->defaultSort('created_at', 'desc')
    //            ->paginated([10, 25, 50]);
    //    }
    //
    //    public function infolist(Infolist $infolist): Infolist
    //    {
    //        /** @var User $user */
    //        $user = Auth::user();
    //
    //        $wallet = $user->wallet()->first();
    //
    //        return $infolist
    //            ->state([
    //                'balance' => $wallet?->balance ?? 0.0,
    //                'loyalty_points' => (int)($user->loyalty_points ?? 0),
    //                'status' => (bool)($wallet?->status ?? true),
    //                'last_operation_at' => $wallet?->last_operation_at,
    //            ])
    //            ->schema([
    //                \Filament\Infolists\Components\Section::make()
    //                    ->heading(__('Overview'))
    //                    ->columns(2)
    //                    ->schema([
    //                        TextEntry::make('loyalty_points')
    //                            ->label(__('Loyalty Points'))
    //                            ->icon(PhosphorIcons::Medal)
    //                            ->suffix(' ' . __('point')),
    //
    //                        TextEntry::make('balance')
    //                            ->label(__('Wallet Balance'))
    //                            ->icon(PhosphorIcons::Coins)
    //                            ->saudiRiyal(),
    //                    ]),
    //
    //                \Filament\Infolists\Components\Section::make()
    //                    ->heading(__('Wallet Status'))
    //                    ->schema([
    //                        IconEntry::make('status')
    //                            ->label(__('Active'))
    //                            ->boolean(),
    //
    //                        TextEntry::make('last_operation_at')
    //                            ->label(__('Last Transaction Date'))
    //                            ->dateTime('d M Y , h:i A')
    //                            ->placeholder(__('Not specified'))
    //                            ->sinceTooltip(),
    //                    ]),
    //            ]);
    //    }

    //    protected function getHeaderActions(): array
    //    {
    //        return [
    //            Action::make('recharge')
    //                ->label(__('Recharge Wallet'))
    //                ->icon(Heroicon::Banknotes)
    //                ->color('success')
    //                ->form([
    //                    \Filament\Forms\Components\TextInput::make('amount')
    //                        ->label(__('Amount'))
    //                        ->numeric()
    //                        ->minValue(1)
    //                        ->required()
    //                        ->saudiRiyal('suffix'),
    //                ])
    //                ->action(function (array $data, MyFatoorahService $myFatoorah) {
    //                    /** @var User $user */
    //                    $user = Auth::user();
    //
    //                    $callbackUrl = route('wallet.topup.callback');
    //
    //                    $note = json_encode([
    //                        'purpose' => 'wallet_topup',
    //                        'user_id' => $user->id,
    //                        'amount' => (float)$data['amount'],
    //                    ]);
    //
    //                    $dto = new PaymentRequestDto(
    //                        amount: (float)$data['amount'],
    //                        customerEmail: (string)($user->email ?? 'noreply@example.com'),
    //                        customerName: (string)($user->name ?? 'User'),
    //                        callbackUrl: $callbackUrl,
    //                        displayCurrency: config('services.myfatoorah.default_currency', 'SAR'),
    //                        note: $note,
    //                    );
    //
    //                    $res = $myFatoorah->generatePaymentLink($dto);
    //
    //                    if (($res['success'] ?? false) && ($res['data']['invoiceURL'] ?? null)) {
    //                        return redirect()->away($res['data']['invoiceURL']);
    //                    }
    //
    //                    \Filament\Notifications\Notification::make()
    //                        ->danger()
    //                        ->title(__('Failed to initialize payment'))
    //                        ->body(__('Please try again later.'))
    //                        ->send();
    //                }),
    //        ];
    //    }

    protected function getUserTransactionsQuery(): Builder
    {
        /** @var User $user */
        $user = Auth::user();

        return WalletTransaction::query()
            ->where('user_id', $user->id);
    }
}
