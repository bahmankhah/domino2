<?php

namespace App\Filament\Resources\OrderResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class IncomesRelationManager extends RelationManager
{
    protected static string $relationship = 'incomes';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('rental.incomes_list');
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('orderItem.good.title')
                    ->label(__('rental.good'))
                    ->description(fn ($record) => $record->orderItem?->good?->code),
                
                Tables\Columns\TextColumn::make('receivedBy.name')
                    ->label(__('rental.recipient')),

                Tables\Columns\TextColumn::make('credit')
                    ->label(__('rental.income'))
                    ->money('IRT')
                    ->color('success'),
                
                Tables\Columns\TextColumn::make('received_at')
                    ->label(__('rental.created_at'))
                    ->dateTime(),
            ]);
    }
}