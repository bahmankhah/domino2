<?php

namespace App\Filament\Resources\LogisticResource\Pages;

use App\Filament\Resources\LogisticResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLogistic extends EditRecord
{
    protected static string $resource = LogisticResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
