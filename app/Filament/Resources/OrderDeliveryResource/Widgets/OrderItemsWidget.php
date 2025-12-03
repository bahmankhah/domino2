<?php

namespace App\Filament\Resources\OrderDeliveryResource\Widgets;

use App\Models\IncomePriceRule;
use App\Models\OrderItem;
use App\Models\OrderItemIncome;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class OrderItemsWidget extends BaseWidget
{
    public ?\App\Models\OrderDelivery $record = null;

    protected int | string | array $columnSpan = 'full';

    public function getHeading(): ?string
    {
        return __('rental.order_items');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                OrderItem::query()
                    ->where('order_id', $this->record->order_id)
                    ->with(['good', 'incomes'])
            )
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label(__('rental.id'))
                    ->sortable(),

                Tables\Columns\TextColumn::make('good_info.title')
                    ->label(__('rental.good'))
                    ->searchable()
                    ->default(fn ($record) => $record->good?->title ?? $record->good_info['title'] ?? '-'),

                Tables\Columns\TextColumn::make('price')
                    ->label(__('rental.price'))
                    ->money('IRT', divideBy: 1)
                    ->sortable(),

                Tables\Columns\TextColumn::make('order_type_info.name')
                    ->label(__('rental.order_type'))
                    ->default(fn ($record) => $record->orderType?->name ?? $record->order_type_info['name'] ?? '-'),

                Tables\Columns\TextColumn::make('started_at')
                    ->label(__('rental.started_at'))
                    ->localeDateTime()
                    ->sortable(),

                Tables\Columns\TextColumn::make('ended_at')
                    ->label(__('rental.ended_at'))
                    ->localeDateTime()
                    ->sortable(),

                Tables\Columns\TextColumn::make('incomes_sum')
                    ->label(__('rental.delivery_income'))
                    ->getStateUsing(function ($record) {
                        $deliveryRule = IncomePriceRule::where('type', 'delivery')->first();
                        if (!$deliveryRule) {
                            return 0;
                        }
                        return $record->incomes()
                            ->where('price_rule_id', $deliveryRule->id)
                            ->sum('credit');
                    })
                    ->formatStateUsing(fn ($state) => number_format($state) . ' IRT')
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\Action::make('add_income')
                    ->label(__('rental.add_income'))
                    ->icon('heroicon-o-plus-circle')
                    ->color('success')
                    ->form([
                        Forms\Components\TextInput::make('credit')
                            ->label(__('rental.delivery_fee'))
                            ->numeric()
                            ->required()
                            ->helperText(__('rental.amount_to_credit')),
                    ])
                    ->action(function (OrderItem $record, array $data) {
                        // Find or create the 'delivery' price rule
                        $deliveryRule = IncomePriceRule::firstOrCreate(
                            ['type' => 'delivery'],
                            ['percentage' => null, 'fallback_type' => null]
                        );

                        OrderItemIncome::create([
                            'order_item_id' => $record->id,
                            'price_rule_id' => $deliveryRule->id,
                            'credit' => $data['credit'],
                            'debit' => 0,
                            'received_by' => auth()->id(),
                            'received_at' => null,
                        ]);

                        Notification::make()
                            ->success()
                            ->title(__('rental.income_added'))
                            ->send();
                    }),

                Tables\Actions\Action::make('view_incomes')
                    ->label(__('rental.view_incomes'))
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    ->modalHeading(fn ($record) => __('rental.delivery_incomes_for_item') . ' #' . $record->id)
                    ->modalContent(function ($record) {
                        $deliveryRule = IncomePriceRule::where('type', 'delivery')->first();
                        $incomes = $deliveryRule 
                            ? $record->incomes()
                                ->where('price_rule_id', $deliveryRule->id)
                                ->with('receivedBy')
                                ->get()
                            : collect();
                        
                        return view('filament.resources.order-delivery.incomes-table', [
                            'incomes' => $incomes,
                        ]);
                    })
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel(__('rental.close'))
                    ->modalWidth('2xl'),
            ]);
    }
}
