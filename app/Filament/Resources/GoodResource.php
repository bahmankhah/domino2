<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GoodResource\Pages;
use App\Filament\Resources\GoodResource\RelationManagers;
use App\Models\Good;
use App\Models\Category;
use App\Models\Warehouse;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
class GoodResource extends Resource
{
    protected static ?string $model = Good::class;
    protected static ?string $navigationIcon = 'heroicon-o-cube';

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->role === 'admin';
    }

    public static function getNavigationLabel(): string
    {
        return __('rental.goods');
    }

    public static function getPluralLabel(): ?string
    {
        return __('rental.goods');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('rental.basic_info'))->schema([
                    Forms\Components\TextInput::make('title')->required()->label(__('rental.title')),
                    Forms\Components\TextInput::make('code')->unique(ignoreRecord: true)->required()->label(__('rental.code')),
                    Forms\Components\Select::make('category_id')
                        ->relationship('category', 'name')
                        ->createOptionForm([
                             Forms\Components\TextInput::make('name')->required(),
                             Forms\Components\TextInput::make('slug')->required(),
                        ])
                        ->searchable(), // Category name usually managed in its own resource
                    Forms\Components\Select::make('warehouse_id')
                        ->label(__('rental.current_location'))
                        ->relationship('warehouse', 'title')
                        ->searchable(),
                    Forms\Components\Toggle::make('is_available')->label(__('rental.is_available'))->default(true),
                ])->columns(2),

                Forms\Components\Section::make(__('rental.media'))->schema([
                    Forms\Components\FileUpload::make('image')->image()->directory('goods'),
                    Forms\Components\Textarea::make('description')->columnSpanFull()->label(__('rental.description')),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image'),
                Tables\Columns\TextColumn::make('title')->searchable()->sortable()->label(__('rental.title')),
                Tables\Columns\TextColumn::make('code')->searchable()->label(__('rental.code')),
                Tables\Columns\TextColumn::make('warehouse.title')->label(__('rental.warehouse')),
                Tables\Columns\IconColumn::make('is_available')->boolean()->label(__('rental.is_available')),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category')->relationship('category', 'name'),
                Tables\Filters\TernaryFilter::make('is_available')->label(__('rental.is_available')),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\ProvidersRelationManager::class,
            RelationManagers\PricesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListGoods::route('/'),
            'create' => Pages\CreateGood::route('/create'),
            'edit' => Pages\EditGood::route('/{record}/edit'),
        ];
    }
}