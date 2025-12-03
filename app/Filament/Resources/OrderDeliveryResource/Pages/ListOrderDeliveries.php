<?php

namespace App\Filament\Resources\OrderDeliveryResource\Pages;

use App\Filament\Resources\OrderDeliveryResource;
use App\Models\Order;
use App\Models\User;
use Filament\Actions;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListOrderDeliveries extends ListRecords
{
    protected static string $resource = OrderDeliveryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->visible(fn () => auth()->user()?->role === 'admin')
                ->form([
                    Forms\Components\Select::make('order_id')
                        ->label(__('rental.order'))
                        ->options(
                            Order::where('status', 'pending')
                                ->get()
                                ->pluck('customer_name', 'id')
                        )
                        ->searchable()
                        ->required(),

                    Forms\Components\Select::make('delivered_by_id')
                        ->label(__('rental.delivered_by'))
                        ->options(
                            User::where('role', 'driver')
                                ->pluck('name', 'id')
                        )
                        ->searchable()
                        ->required(),
                ])
                ->mutateFormDataUsing(function (array $data): array {
                    $data['delivered_at'] = null;
                    $data['fee'] = null;
                    return $data;
                })
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title(__('rental.delivery_assigned'))
                        ->body(__('rental.delivery_assigned_to_driver'))
                ),
        ];
    }
}
