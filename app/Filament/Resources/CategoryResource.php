<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CategoryResource\Pages;
use App\Models\Category;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;
    protected static ?string $navigationIcon = 'heroicon-o-tag';

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->role === 'admin';
    }

    public static function getNavigationLabel(): string
    {
        return __('rental.categories');
    }

    public static function getPluralLabel(): ?string
    {
        return __('rental.categories');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn (string $operation, $state, Forms\Set $set) => $operation === 'create' ? $set('slug', Str::slug($state)) : null)
                    ->label(__('rental.name')),
                Forms\Components\TextInput::make('slug')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->label(__('rental.slug')),
                Forms\Components\ColorPicker::make('color')
                    ->label(__('rental.color')),
                Forms\Components\Toggle::make('is_active')
                    ->default(true)
                    ->label(__('rental.is_active')),
                Forms\Components\Textarea::make('description')
                    ->label(__('rental.description'))
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ColorColumn::make('color')->label(__('rental.color')),
                Tables\Columns\TextColumn::make('name')->searchable()->sortable()->label(__('rental.name')),
                Tables\Columns\TextColumn::make('slug')->label(__('rental.slug')),
                Tables\Columns\IconColumn::make('is_active')->boolean()->label(__('rental.is_active')),
                Tables\Columns\TextColumn::make('created_at')->localeDateTime()->label(__('rental.created_at')),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCategories::route('/'),
            'create' => Pages\CreateCategory::route('/create'),
            'edit' => Pages\EditCategory::route('/{record}/edit'),
        ];
    }
}