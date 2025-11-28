<?php

namespace App\Filament\Resources\GoodResource\Pages;

use App\Filament\Resources\GoodResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditGood extends EditRecord
{
    protected static string $resource = GoodResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
