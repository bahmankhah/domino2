<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class UserResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?string $navigationIcon = 'heroicon-o-users';

    public static function getNavigationLabel(): string
    {
        return __('rental.user');
    }

    public static function getPluralLabel(): ?string
    {
        return __('rental.user');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('rental.basic_info'))->schema([
                    Forms\Components\TextInput::make('name')
                        ->label(__('rental.name'))
                        ->required(),
                    Forms\Components\TextInput::make('email')
                        ->email()
                        ->required(),
                    Forms\Components\TextInput::make('mobile')
                        ->label(__('rental.mobile'))
                        ->tel()
                        ->required(),
                    Forms\Components\Select::make('role')
                        ->label(__('rental.role'))
                        ->options([
                            'customer' => __('rental.roles.customer'),
                            'admin' => __('rental.roles.admin'),
                            'delivery' => __('rental.roles.delivery'),
                        ])
                        ->required(),
                    Forms\Components\TextInput::make('wallet')
                        ->label(__('rental.balance'))
                        ->numeric()
                        ->suffix(__('rental.currency'))
                        ->readOnly(), 
                    Forms\Components\Textarea::make('address')
                        ->label(__('rental.address'))
                        ->columnSpanFull(),
                ])->columns(2),
                
                Forms\Components\Section::make('Security')->schema([
                    Forms\Components\TextInput::make('password')
                        ->password()
                        ->dehydrated(fn ($state) => filled($state))
                        ->required(fn (string $context): bool => $context === 'create'),
                ])->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('rental.name'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('mobile')
                    ->label(__('rental.mobile'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('wallet')
                    ->label(__('rental.balance'))
                    ->money('IRT')
                    ->sortable()
                    ->color(fn (string $state): string => $state < 0 ? 'danger' : 'success'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('rental.created_at'))
                    ->dateTime(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'view' => Pages\ViewUser::route('/{record}'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}