<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use App\Services\FinancialService;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditOrder extends EditRecord
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function afterSave(): void
    {
        // Recalculate incomes for all order items after the order is updated
        $financialService = app(FinancialService::class);
        foreach ($this->record->items as $orderItem) {
            $financialService->calculateItemIncomes($orderItem);
        }
    }
}
