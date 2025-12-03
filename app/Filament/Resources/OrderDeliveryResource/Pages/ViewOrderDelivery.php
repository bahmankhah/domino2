<?php

namespace App\Filament\Resources\OrderDeliveryResource\Pages;

use App\Filament\Resources\OrderDeliveryResource;
use App\Models\OrderItemIncome;
use Filament\Actions;
use Filament\Forms;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Filament\Tables;
use Filament\Tables\Table;

class ViewOrderDelivery extends ViewRecord
{
    protected static string $resource = OrderDeliveryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('mark_delivered')
                ->label(__('rental.mark_as_delivered'))
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->visible(fn () => !$this->record->delivered_at)
                ->form([
                    Forms\Components\Checkbox::make('cash_from_customer')
                        ->label(__('rental.cash_from_customer'))
                        ->helperText(__('rental.cash_from_customer_help'))
                        ->live()
                        ->afterStateUpdated(function ($state, Forms\Set $set) {
                            if ($state) {
                                $set('fee', 0);
                            }
                        }),

                    Forms\Components\TextInput::make('fee')
                        ->label(__('rental.delivery_fee'))
                        ->numeric()
                        ->required()
                        ->default($this->record->fee ?? 0)
                        ->disabled(fn (Forms\Get $get) => $get('cash_from_customer'))
                        ->dehydrated(),
                ])
                ->action(function (array $data) {
                    $this->record->update([
                        'fee' => $data['fee'] ?? 0,
                        'delivered_at' => now(),
                    ]);

                    // Check if all deliveries for this order are completed
                    $order = $this->record->order;
                    $allDelivered = $order->deliveries()
                        ->whereNull('delivered_at')
                        ->count() === 0;

                    if ($allDelivered) {
                        $order->update(['status' => 'in-rent']);
                        
                        Notification::make()
                            ->success()
                            ->title(__('rental.delivery_completed'))
                            ->body(__('rental.order_status_updated_to_in_rent'))
                            ->send();
                    } else {
                        Notification::make()
                            ->success()
                            ->title(__('rental.delivery_marked_as_delivered'))
                            ->send();
                    }

                    // Redirect to index page
                    return redirect()->to(OrderDeliveryResource::getUrl('index'));
                }),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make(__('rental.delivery_info'))
                    ->schema([
                        Infolists\Components\TextEntry::make('order.id')
                            ->label(__('rental.order_id')),
                        
                        Infolists\Components\TextEntry::make('status')
                            ->label(__('rental.status'))
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'pending' => 'warning',
                                'delivered' => 'success',
                                default => 'gray',
                            }),
                        
                        Infolists\Components\TextEntry::make('fee')
                            ->label(__('rental.delivery_fee'))
                            ->money('IRT', divideBy: 1),
                        
                        Infolists\Components\TextEntry::make('delivered_at')
                            ->label(__('rental.delivered_at'))
                            ->localeDateTime(),
                        
                        Infolists\Components\TextEntry::make('deliveredBy.name')
                            ->label(__('rental.delivered_by')),
                    ])
                    ->columns(2),

                Infolists\Components\Section::make(__('rental.customer_info'))
                    ->schema([
                        Infolists\Components\TextEntry::make('order.customer_name')
                            ->label(__('rental.customer_name')),
                        
                        Infolists\Components\TextEntry::make('order.customer_mobile')
                            ->label(__('rental.customer_mobile')),
                        
                        Infolists\Components\TextEntry::make('order.customer_address')
                            ->label(__('rental.customer_address'))
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }

    protected function getFooterWidgets(): array
    {
        return [
            OrderDeliveryResource\Widgets\OrderItemsWidget::class,
        ];
    }
}