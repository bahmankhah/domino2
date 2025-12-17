<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderTypeResource\Pages;
use App\Models\OrderType;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class OrderTypeResource extends Resource
{
    protected static ?string $model = OrderType::class;
    protected static ?string $navigationIcon = 'heroicon-o-clock';

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->role === 'admin';
    }

    public static function getNavigationLabel(): string
    {
        return __('rental.order_types');
    }

    public static function getPluralLabel(): ?string
    {
        return __('rental.order_types');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->label(__('rental.name')),
                Forms\Components\TextInput::make('duration_days')
                    ->numeric()
                    ->default(1)
                    ->required()
                    ->label(__('rental.days')),
                Forms\Components\Textarea::make('description')
                    ->label(__('rental.description'))
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label(__('rental.name'))->sortable(),
                Tables\Columns\TextColumn::make('duration_days')->label(__('rental.days'))->sortable(),
                Tables\Columns\TextColumn::make('description')->limit(50)->label(__('rental.description')),
            ])
            ->defaultSort('name', 'asc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrderTypes::route('/'),
            'create' => Pages\CreateOrderType::route('/create'),
            'edit' => Pages\EditOrderType::route('/{record}/edit'),
        ];
    }
}