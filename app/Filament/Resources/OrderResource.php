<?php

namespace App\Filament\Resources;

// ... imports ...
use App\Filament\Resources\OrderResource\Pages;
use App\Filament\Resources\OrderResource\RelationManagers;
use App\Models\Order;
use App\Models\Good;
use App\Models\OrderType;
use App\Models\Logistic;
use App\Models\Warehouse;
use App\Models\User;
use App\Models\OrderTypeGoodPrice;
use App\Services\FinancialService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use Carbon\Carbon;

class OrderResource extends Resource
{
    // ... basic config ...
    protected static ?string $model = Order::class;
    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';
    protected static ?int $navigationSort = 1;

    public static function getNavigationLabel(): string { return __('rental.orders_rents'); }
    public static function getPluralLabel(): ?string { return __('rental.orders_rents'); }

    public static function form(Form $form): Form
    {
        // ... (Form schema remains identical to previous version) ...
        return $form
            ->schema([
                Forms\Components\Wizard::make([
                    Forms\Components\Wizard\Step::make('customer_info')
                        ->label(__('rental.customer'))
                        ->schema([
                            Forms\Components\Select::make('customer_id')
                                ->label(__('rental.customer'))
                                ->options(User::all()->pluck('name', 'id'))
                                ->searchable()
                                ->live()
                                ->afterStateUpdated(function ($state, Forms\Set $set) {
                                    if ($user = User::find($state)) {
                                        $set('customer_name', $user->name);
                                        $set('customer_mobile', $user->mobile);
                                        $set('customer_address', $user->address ?? ''); 
                                    }
                                }),
                            Forms\Components\TextInput::make('customer_name')->required()->label(__('rental.name')),
                            Forms\Components\TextInput::make('customer_mobile')->required()->tel()->label(__('rental.mobile')),
                            Forms\Components\Textarea::make('customer_address')->required()->columnSpanFull()->label(__('rental.address')),
                            Forms\Components\Toggle::make('has_collateral')->label(__('rental.has_collateral'))->default(true),
                        ])->columns(2),

                    Forms\Components\Wizard\Step::make('items')
                        ->label(__('rental.items'))
                        ->schema([
                            Forms\Components\Repeater::make('items')
                                ->label(__('rental.items'))
                                ->relationship()
                                ->schema([
                                    Forms\Components\Group::make()->schema([
                                        Forms\Components\Select::make('good_id')
                                            ->label(__('rental.good'))
                                            ->options(Good::where('is_available', true)->pluck('title', 'id'))
                                            ->searchable()
                                            ->required()
                                            ->live()
                                            ->afterStateUpdated(function ($state, Forms\Set $set, Forms\Get $get) {
                                                self::updatePrice($state, $get('order_type_id'), $set);
                                                if ($good = Good::find($state)) {
                                                    $set('warehouse_id', $good->warehouse_id);
                                                    $set('warehouse_display', $good->warehouse?->title ?? '-');
                                                }
                                            }),
                                        Forms\Components\Select::make('order_type_id')
                                            ->label(__('rental.duration_type'))
                                            ->options(OrderType::pluck('name', 'id'))
                                            ->required()
                                            ->live()
                                            ->afterStateUpdated(function ($state, Forms\Set $set, Forms\Get $get) {
                                                self::updatePrice($get('good_id'), $state, $set);
                                                if ($type = OrderType::find($state)) {
                                                    $now = Carbon::now();
                                                    $set('started_at', $now->toDateTimeString());
                                                    $set('ended_at', $now->copy()->addDays($type->duration_days)->toDateTimeString());
                                                }
                                            }),
                                    ])->columns(2),

                                    Forms\Components\Group::make()->schema([
                                        Forms\Components\Hidden::make('warehouse_id')->required(),
                                        Forms\Components\Placeholder::make('warehouse_display')
                                            ->label(__('rental.warehouse'))
                                            ->content(fn (Forms\Get $get) => $get('warehouse_id') ? Warehouse::find($get('warehouse_id'))?->title : '-'),
                                        Forms\Components\Select::make('logistic_id')
                                            ->label(__('rental.logistic'))
                                            ->options(Logistic::pluck('name', 'id'))
                                            ->searchable(),
                                    ])->columns(2),

                                    Forms\Components\DateTimePicker::make('started_at')->required()->label(__('rental.start_date')),
                                    Forms\Components\DateTimePicker::make('ended_at')->required()->label(__('rental.end_date')),
                                    Forms\Components\TextInput::make('price')->label(__('rental.price'))->numeric()->required()->suffix(__('rental.currency')), 
                                ])
                                ->columns(1)
                                ->itemLabel(fn (array $state): ?string => isset($state['good_id']) ? Good::find($state['good_id'])?->title : null),
                        ]),

                    Forms\Components\Wizard\Step::make('status')
                        ->label(__('rental.status'))
                        ->schema([
                            Forms\Components\Select::make('status')
                                ->label(__('rental.status'))
                                ->options([
                                    'pending' => __('rental.pending'),
                                    'delivery' => __('rental.delivery'),
                                    'in-rent' => __('rental.in_rent'),
                                    'completed' => __('rental.completed'),
                                    'canceled' => __('rental.canceled'),
                                ])
                                ->default('in-rent')
                                ->required(),
                            Forms\Components\Textarea::make('description')->label(__('rental.internal_notes')),
                        ]),
                ])->columnSpanFull(),
            ]);
    }

    public static function updatePrice($goodId, $orderTypeId, Forms\Set $set)
    {
        if (!$goodId || !$orderTypeId) return;
        $pricing = OrderTypeGoodPrice::where('good_id', $goodId)->where('order_type_id', $orderTypeId)->first();
        if ($pricing) $set('price', $pricing->price);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->sortable(),
                Tables\Columns\TextColumn::make('customer_name')->searchable()->label(__('rental.customer')),
                Tables\Columns\TextColumn::make('status')
                    ->label(__('rental.status'))
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => __('rental.pending'),
                        'delivery' => __('rental.delivery'),
                        'in-rent' => __('rental.in_rent'),
                        'completed' => __('rental.completed'),
                        'canceled' => __('rental.canceled'),
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'gray',
                        'in-rent' => 'warning',
                        'completed' => 'success',
                        'canceled' => 'danger',
                        default => 'primary',
                    }),
                Tables\Columns\TextColumn::make('items_count')->counts('items')->label(__('rental.items_count')),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->label(__('rental.created_at')),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make('to_delivery')
                        ->label(__('rental.mark_delivery'))
                        ->icon('heroicon-o-truck')
                        ->color('warning')
                        ->visible(fn (Order $record) => $record->status === 'pending')
                        ->action(fn (Order $record) => $record->update(['status' => 'delivery'])),

                    Tables\Actions\Action::make('to_rent')
                        ->label(__('rental.mark_in_rent'))
                        ->icon('heroicon-o-play')
                        ->color('primary')
                        ->visible(fn (Order $record) => $record->status === 'delivery')
                        ->action(fn (Order $record) => $record->update(['status' => 'in-rent'])),

                    // UPDATED ACTION: Uses commitOrderTransactions
                    Tables\Actions\Action::make('complete')
                        ->label(__('rental.complete_distribute'))
                        ->icon('heroicon-o-currency-dollar')
                        ->color('success')
                        ->requiresConfirmation()
                        ->visible(fn (Order $record) => $record->status === 'in-rent')
                        ->action(function (Order $record, FinancialService $service) {
                            $service->commitOrderTransactions($record);
                            Notification::make()->title(__('rental.order_completed_msg'))->success()->send();
                        }),

                    Tables\Actions\Action::make('cancel')
                        ->label(__('rental.mark_canceled'))
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->visible(fn (Order $record) => in_array($record->status, ['pending', 'delivery', 'in-rent']))
                        ->action(fn (Order $record) => $record->update(['status' => 'canceled'])),
                ])
                ->icon('heroicon-m-ellipsis-vertical'),
            ]);
    }
    
    public static function getRelations(): array
    {
        return [
            RelationManagers\IncomesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'view' => Pages\ViewOrder::route('/{record}'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}