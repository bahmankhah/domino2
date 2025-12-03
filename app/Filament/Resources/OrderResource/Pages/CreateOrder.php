<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use App\Models\Good;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateOrder extends CreateRecord
{
    protected function afterCreate(): void
    {
        foreach($this->record->orderItems as $orderItem){
            /**
             * @var Good
             */
            $good = $orderItem->good;
            $good->is_available = false;
            $good->save();
        }
    }

    protected static string $resource = OrderResource::class;
}
