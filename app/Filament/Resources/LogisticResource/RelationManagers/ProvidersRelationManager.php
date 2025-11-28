<?php

namespace App\Filament\Resources\LogisticResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class ProvidersRelationManager extends RelationManager
{
    protected static string $relationship = 'providers';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('rental.logistic_owners');
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('provider_id')
                    ->relationship('providers', 'name')
                    ->searchable()
                    ->required()
                    ->label(__('rental.user')),
                Forms\Components\TextInput::make('ownership_percent')
                    ->label(__('rental.ownership_percent'))
                    ->numeric()
                    ->suffix('%')
                    ->maxValue(100)
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label(__('rental.user')),
                Tables\Columns\TextColumn::make('pivot.ownership_percent')->label(__('rental.ownership_percent')),
            ])
            ->headerActions([
                Tables\Actions\AttachAction::make()
                    ->form(fn (Tables\Actions\AttachAction $action): array => [
                        $action->getRecordSelect()->label(__('rental.user')),
                        Forms\Components\TextInput::make('ownership_percent')
                            ->label(__('rental.ownership_percent'))
                            ->required()
                            ->numeric()
                            ->default(100),
                    ]),
            ])
            ->actions([
                Tables\Actions\DetachAction::make(),
                Tables\Actions\EditAction::make(),
            ]);
    }
}