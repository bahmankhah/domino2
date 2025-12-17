<?php

namespace App\Filament\Resources\GoodResource\RelationManagers;

use App\Models\OrderType;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
class PricesRelationManager extends RelationManager
{
    protected static string $relationship = 'prices';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('rental.rent_prices');
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('order_type_id')
                    ->label(__('rental.duration_type'))
                    ->options(OrderType::pluck('name', 'id'))
                    ->searchable()
                    ->preload()
                    ->required()
                    ->getSearchResultsUsing(fn (string $search): array => 
                        OrderType::where('name', 'like', "%{$search}%")
                            ->limit(50)
                            ->pluck('name', 'id')
                            ->toArray()
                    )
                    ->getOptionLabelUsing(fn ($value): ?string => 
                        OrderType::find($value)?->name
                    ),
                Forms\Components\TextInput::make('price')
                    ->label(__('rental.price'))
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('supplier_price')
                    ->label(__('rental.supplier_price'))
                    ->numeric()
                    ->nullable(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label(__('rental.duration_type')),
                Tables\Columns\TextColumn::make('pivot.price')->money('IRT')->label(__('rental.price')),
                Tables\Columns\TextColumn::make('pivot.supplier_price')->money('IRT')->label(__('rental.supplier_price')),
            ])
            ->headerActions([
                Tables\Actions\AttachAction::make()
                    ->form(fn (Tables\Actions\AttachAction $action): array => [
                        $action->getRecordSelect()
                            ->label(__('rental.duration_type'))
                            ->options(OrderType::pluck('name', 'id'))
                            ->searchable()
                            ->preload()
                            ->getSearchResultsUsing(fn (string $search): array => 
                                OrderType::where('name', 'like', "%{$search}%")
                                    ->limit(50)
                                    ->pluck('name', 'id')
                                    ->toArray()
                            )
                            ->getOptionLabelUsing(fn ($value): ?string => 
                                OrderType::find($value)?->name
                            ),
                        Forms\Components\TextInput::make('price')->label(__('rental.price'))->required()->numeric(),
                        Forms\Components\TextInput::make('supplier_price')->label(__('rental.supplier_price'))->numeric()->nullable(),
                    ]),
            ])
            ->actions([
                Tables\Actions\DetachAction::make(),
                Tables\Actions\EditAction::make()
                    ->form([
                        Forms\Components\Select::make('order_type_id')
                            ->label(__('rental.duration_type'))
                            ->options(OrderType::pluck('name', 'id'))
                            ->searchable()
                            ->preload()
                            ->required()
                            ->getSearchResultsUsing(fn (string $search): array => 
                                OrderType::where('name', 'like', "%{$search}%")
                                    ->limit(50)
                                    ->pluck('name', 'id')
                                    ->toArray()
                            )
                            ->getOptionLabelUsing(fn ($value): ?string => 
                                OrderType::find($value)?->name
                            ),
                        Forms\Components\TextInput::make('price')
                            ->label(__('rental.price'))
                            ->required()
                            ->numeric(),
                        Forms\Components\TextInput::make('supplier_price')
                            ->label(__('rental.supplier_price'))
                            ->numeric()
                            ->nullable(),
                    ]),
            ]);
    }
}