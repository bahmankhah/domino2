<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Models\User;
use App\Services\FinancialService;
use Filament\Actions;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewUser extends ViewRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            
            Actions\Action::make('settlement')
                ->label('Settlement / Payout') // You can use __('rental.settlement')
                ->icon('heroicon-o-banknotes')
                ->color('success')
                ->visible(fn (User $record) => $record->wallet > 0)
                ->form([
                    Forms\Components\TextInput::make('amount')
                        ->label(__('rental.price')) // Reusing price/amount label
                        ->numeric()
                        ->required()
                        ->default(fn (User $record) => $record->wallet)
                        ->maxValue(fn (User $record) => $record->wallet)
                        ->suffix(__('rental.currency')),
                    
                    Forms\Components\Textarea::make('description')
                        ->label(__('rental.description'))
                        ->default('Settlement Payout'),
                ])
                ->action(function (User $record, array $data, FinancialService $service) {
                    try {
                        $service->processSettlement($record, $data['amount'], $data['description']);
                        
                        Notification::make()
                            ->title('Settlement Successful')
                            ->success()
                            ->send();
                            
                    } catch (\Exception $e) {
                        Notification::make()
                            ->title('Error')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),
        ];
    }
}