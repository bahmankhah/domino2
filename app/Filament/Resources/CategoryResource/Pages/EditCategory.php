<?php

namespace App\Filament\Resources\CategoryResource\Pages;

use App\Filament\Resources\CategoryResource;
use App\Models\OrderType;
use App\Models\OrderTypeGoodPrice;
use Filament\Actions;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditCategory extends EditRecord
{
    protected static string $resource = CategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\Action::make('managePrices')
                ->label(__('rental.manage_prices'))
                ->icon('heroicon-o-currency-dollar')
                ->modalHeading(__('rental.manage_prices_heading'))
                ->modalSubmitActionLabel(__('rental.sync_prices_to_goods'))
                ->form($this->getPriceFormSchema())
                ->action(function (array $data) {
                    $category = $this->record;
                    $prices = $data['prices'] ?? [];

                    foreach ($category->goods as $good) {
                        foreach ($prices as $orderTypeId => $price) {
                            if ($price === null) {

                                OrderTypeGoodPrice::where('order_type_id', $orderTypeId)
                                    ->where('good_id', $good->id)
                                    ->delete();
                            } else {
                                OrderTypeGoodPrice::updateOrCreate(
                                    [
                                        'order_type_id' => $orderTypeId,
                                        'good_id' => $good->id,
                                    ],
                                    [
                                        'price' => $price,
                                    ]
                                );
                            }
                        }
                    }

                    Notification::make()
                        ->title(__('rental.prices_updated_successfully'))
                        ->success()
                        ->send();
                }),

        ];
    }
    protected function getPriceFormSchema(): array
    {
        return [
            Section::make('Order Type Prices')
                ->schema(
                    OrderType::all()->map(function ($type) {
                        return TextInput::make("prices.{$type->id}")
                            ->label("{$type->name} Price")
                            ->numeric()
                            ->nullable();
                    })->toArray()
                )
        ];
    }

}
