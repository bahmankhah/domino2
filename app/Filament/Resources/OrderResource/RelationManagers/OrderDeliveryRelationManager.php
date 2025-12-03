<?php

namespace App\Filament\Resources\OrderResource\RelationManagers;

use App\Models\OrderDelivery;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class OrderDeliveryRelationManager extends RelationManager
{
    protected static string $relationship = 'deliveries'; // Order::deliveries()

    protected static ?string $title = null;

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('rental.order_deliveries'); // translation
    }

    public static function getModelLabel(): string
    {
        return __('rental.order_delivery'); // singular
    }

    public static function getPluralModelLabel(): string
    {
        return __('rental.order_deliveries'); // plural
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('delivered_by_id')
                    ->label(__('rental.delivered_by'))
                    ->relationship('deliveredBy', 'name')
                    ->searchable()
                    ->required(),

                Forms\Components\DateTimePicker::make('delivered_at')
                    ->label(__('rental.delivered_at'))
                    ->required(),

                Forms\Components\TextInput::make('fee')
                    ->label(__('rental.delivery_fee'))
                    ->numeric()
                    ->suffix(__('rental.currency'))
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('deliveredBy.name')
                    ->label(__('rental.delivered_by'))
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('delivered_at')
                    ->label(__('rental.delivered_at'))
                    ->dateTime()
                    ->sortable(),

                Tables\Columns\TextColumn::make('fee')
                    ->label(__('rental.delivery_fee'))
                    ->money('IRT') // or IRR etc.
                    ->sortable(),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label(__('rental.add_delivery')),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label(__('rental.edit')),
                Tables\Actions\DeleteAction::make()
                    ->label(__('rental.delete')),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make()
                    ->label(__('rental.delete_selected')),
            ]);
    }
}
