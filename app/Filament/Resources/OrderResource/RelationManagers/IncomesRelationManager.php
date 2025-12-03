<?php

namespace App\Filament\Resources\OrderResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use App\Models\OrderItemIncome;
use App\Models\IncomePriceRule;
use App\Models\User;

class IncomesRelationManager extends RelationManager
{
    protected static string $relationship = 'incomes';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('rental.incomes_list');
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('order_item_id')
                    ->label(__('rental.good'))
                    // Fetch items belonging to the current Order
                    ->options(function (RelationManager $livewire) {
                        return $livewire->getOwnerRecord()->items->mapWithKeys(function ($item) {
                            $title = $item->good?->title ?? 'Unknown';
                            $type = $item->orderType?->name ?? '-';
                            return [$item->id => "{$title} ({$type})"];
                        });
                    })
                    ->required()
                    ->searchable()
                    ->preload(),

                Forms\Components\Select::make('received_by')
                    ->label(__('rental.recipient'))
                    ->options(User::all()->pluck('name', 'id'))
                    ->required()
                    ->searchable()
                    ->preload(),

                // Only allow selecting rules that are "Manual" (percentage is null)
                Forms\Components\Select::make('price_rule_id')
                    ->label(__('rental.income_type') ?? 'Type')
                    ->options(IncomePriceRule::pluck('type', 'id'))
                    ->required()
                    ->preload(),

                Forms\Components\TextInput::make('credit')
                    ->label(__('rental.income'))
                    ->numeric()
                    ->required()
                    ->suffix(__('rental.currency')),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('orderItem.good.title')
                    ->label(__('rental.good'))
                    ->description(fn ($record) => $record->orderItem?->good?->code),
                
                Tables\Columns\TextColumn::make('recipient.name')
                    ->label(__('rental.recipient')),

                Tables\Columns\TextColumn::make('credit')
                    ->label(__('rental.income'))
                    ->money('IRT')
                    ->color('success'),
                
                Tables\Columns\TextColumn::make('received_at')
                    ->label(__('rental.created_at'))
                    ->dateTime(),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label(__('rental.add_manual_income') ?? 'Add Manual Income')
                    // HasManyThrough cannot create directly, so we handle creation manually
                    ->using(function (array $data, string $model): Model {
                        return OrderItemIncome::create([
                            'order_item_id' => $data['order_item_id'],
                            'received_by' => $data['received_by'],
                            'price_rule_id' => $data['price_rule_id'],
                            'credit' => $data['credit'],
                            'debit' => 0,
                        ]);
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }
}