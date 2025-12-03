<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TransactionResource\Pages;
use App\Models\Transaction;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class TransactionResource extends Resource
{
    protected static ?string $model = Transaction::class;
    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->role === 'admin';
    }

    public static function getNavigationGroup(): ?string
    {
        return __('rental.finance');
    }

    public static function getNavigationLabel(): string
    {
        return __('rental.transactions');
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')->searchable()->sortable()->label(__('rental.user')),
                Tables\Columns\TextColumn::make('type')
                    // Currently type is stored as 'income' or other string. 
                    // To translate the value, you can use formatStateUsing or similar logic if the DB values are fixed keys.
                    ->formatStateUsing(fn (string $state): string => match($state) {
                        'income' => __('rental.income') ?? $state,
                        'expense' => __('rental.expense') ?? $state,
                        default => $state,
                    })
                    ->badge()
                    ->color(fn (string $state): string => $state === 'income' ? 'success' : 'danger'),
                Tables\Columns\TextColumn::make('credit')->money('IRT')->color('success')->label(__('rental.credit')),
                Tables\Columns\TextColumn::make('debit')->money('IRT')->color('danger')->label(__('rental.debit')),
                Tables\Columns\TextColumn::make('remain')->label(__('rental.balance'))->money('IRT'),
                Tables\Columns\TextColumn::make('description')->limit(30)->label(__('rental.description')),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->label(__('rental.created_at')),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('user')->relationship('user', 'name')->label(__('rental.user')),
            ]);
    }
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTransactions::route('/'),
        ];
    }
}