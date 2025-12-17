<?php

namespace App\Filament\Resources\OrderResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class TransactionsRelationManager extends RelationManager
{
    protected static string $relationship = 'transactions';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('rental.transactions');
    }

    public function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label(__('rental.user'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('type')
                    ->label(__('rental.type'))
                    ->formatStateUsing(fn (string $state): string => match($state) {
                        'income' => __('rental.income'),
                        'expense' => __('rental.expense'),
                        'settlement' => __('rental.settlement'),
                        default => $state,
                    })
                    ->colors([
                        'success' => 'income',
                        'danger' => 'expense',
                        'warning' => 'settlement',
                    ]),
                Tables\Columns\TextColumn::make('credit')
                    ->label(__('rental.credit'))
                    ->money('IRT')
                    ->color('success')
                    ->sortable(),
                Tables\Columns\TextColumn::make('debit')
                    ->label(__('rental.debit'))
                    ->money('IRT')
                    ->color('danger')
                    ->sortable(),
                Tables\Columns\TextColumn::make('remain')
                    ->label(__('rental.balance'))
                    ->money('IRT')
                    ->sortable(),
                Tables\Columns\TextColumn::make('description')
                    ->label(__('rental.description'))
                    ->limit(50)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= 50) {
                            return null;
                        }
                        return $state;
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('rental.created_at'))
                    ->localeDateTime()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label(__('rental.type'))
                    ->options([
                        'income' => __('rental.income'),
                        'expense' => __('rental.expense'),
                        'settlement' => __('rental.settlement'),
                    ]),
                Tables\Filters\SelectFilter::make('user')
                    ->relationship('user', 'name')
                    ->label(__('rental.user')),
            ])
            ->headerActions([
                // No header actions - read-only
            ])
            ->actions([
                // No actions - read-only
            ])
            ->bulkActions([
                // No bulk actions - read-only
            ]);
    }
}
