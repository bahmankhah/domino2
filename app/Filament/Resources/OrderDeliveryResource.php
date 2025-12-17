<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderDeliveryResource\Pages\ListOrderDeliveries;
use App\Filament\Resources\OrderDeliveryResource\Pages\ViewOrderDelivery;
use App\Models\OrderDelivery;
use Filament\Forms;
use Filament\Tables;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;

class OrderDeliveryResource extends Resource
{
    protected static ?string $model = OrderDelivery::class;

    protected static ?string $navigationIcon = 'heroicon-o-truck';

    public static function getNavigationGroup(): ?string
    {
        return __('rental.driver');
    }

    public static function getNavigationLabel(): string
    {
        return __('rental.order_deliveries');
    }

    public static function getPluralLabel(): ?string
    {
        return __('rental.order_deliveries');
    }

    public static function getLabel(): ?string
    {
        return __('rental.order_delivery');
    }

    public static function shouldRegisterNavigation(): bool
    {
        // return true;
        $role = auth()->user()?->role;
        return in_array($role, ['admin', 'delivery']);
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user = auth()->user();

        if ($user->role === 'delivery') {
            // Drivers only see their own undelivered orders
            $query->where('delivered_by_id', $user->id)
                  ->whereNull('delivered_at');
        }
        // Admin sees all deliveries (no filter)

        return $query;
    }
    /**
     * Form not used - drivers use the "Mark as Delivered" action
     */
    public static function form(Forms\Form $form): Forms\Form
    {
        return $form->schema([]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('order.id')
                ->label(__('rental.order_id'))
                ->sortable()
                ->searchable(),

            Tables\Columns\TextColumn::make('order.customer_name')
                ->label(__('rental.customer'))
                ->searchable(),

            Tables\Columns\BadgeColumn::make('status')
                ->label(__('rental.status'))
                ->getStateUsing(fn ($record) => $record->delivered_at ? 'delivered' : 'pending')
                ->colors([
                    'warning' => 'pending',
                    'success' => 'delivered',
                ])
                ->formatStateUsing(fn (string $state): string => __('rental.' . $state)),

            Tables\Columns\TextColumn::make('fee')
                ->label(__('rental.delivery_fee'))
                ->money('IRT', divideBy: 1)
                ->sortable(),

            Tables\Columns\TextColumn::make('delivered_at')
                ->label(__('rental.delivered_at'))
                ->localeDateTime()
                ->sortable()
                ->placeholder(__('rental.not_delivered_yet')),

            Tables\Columns\TextColumn::make('created_at')
                ->label(__('rental.created_at'))
                ->localeDateTime()
                ->sortable(),
        ])
        ->actions([
            Tables\Actions\ViewAction::make(),
        ])
        ->defaultSort('created_at', 'desc');
    }
    public static function getPages(): array
    {
        return [
            'index' => ListOrderDeliveries::route('/'),
            'view' => ViewOrderDelivery::route('/{record}'),
        ];
    }

    public static function getWidgets(): array
    {
        return [
            OrderDeliveryResource\Widgets\OrderItemsWidget::class,
        ];
    }
}
