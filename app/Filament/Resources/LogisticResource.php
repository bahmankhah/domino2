<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LogisticResource\Pages;
use App\Filament\Resources\LogisticResource\RelationManagers;
use App\Models\Logistic;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class LogisticResource extends Resource
{
    protected static ?string $model = Logistic::class;
    protected static ?string $navigationIcon = 'heroicon-o-truck';

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->role === 'admin';
    }

    public static function getNavigationLabel(): string
    {
        return __('rental.logistics');
    }

    public static function getPluralLabel(): ?string
    {
        return __('rental.logistics');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->label(__('rental.name')),
                Forms\Components\FileUpload::make('image')
                    ->image()
                    ->directory('logistics')
                    ->label(__('rental.media')),
                Forms\Components\Textarea::make('description')
                    ->label(__('rental.description'))
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')->label(__('rental.media')),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->label(__('rental.name')),
                Tables\Columns\TextColumn::make('description')
                    ->limit(50)
                    ->label(__('rental.description')),
                Tables\Columns\TextColumn::make('created_at')->localeDateTime()->label(__('rental.created_at')),
            ])
            ->filters([])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\ProvidersRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLogistics::route('/'),
            'create' => Pages\CreateLogistic::route('/create'),
            'edit' => Pages\EditLogistic::route('/{record}/edit'),
        ];
    }
}