<?php

namespace App\Filament\Resources\GoodResource\RelationManagers;

use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

// --- 1. Providers Relation Manager ---
class ProvidersRelationManager extends RelationManager
{
    protected static string $relationship = 'providers';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('rental.investors_owners');
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('provider_id')
                    ->options(User::pluck('name', 'id'))
                    ->searchable()
                    ->preload()
                    ->required()
                    ->label(__('rental.user'))
                    ->getSearchResultsUsing(fn (string $search): array => 
                        User::where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%")
                            ->orWhere('mobile', 'like', "%{$search}%")
                            ->limit(50)
                            ->pluck('name', 'id')
                            ->toArray()
                    )
                    ->getOptionLabelUsing(fn ($value): ?string => 
                        User::find($value)?->name
                    ),
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
                        $action->getRecordSelect()
                            ->label(__('rental.user'))
                            ->options(User::pluck('name', 'id'))
                            ->searchable()
                            ->preload()
                            ->getSearchResultsUsing(fn (string $search): array => 
                                User::where('name', 'like', "%{$search}%")
                                    ->orWhere('email', 'like', "%{$search}%")
                                    ->orWhere('mobile', 'like', "%{$search}%")
                                    ->limit(50)
                                    ->pluck('name', 'id')
                                    ->toArray()
                            )
                            ->getOptionLabelUsing(fn ($value): ?string => 
                                User::find($value)?->name
                            ),
                        Forms\Components\TextInput::make('ownership_percent')->label(__('rental.ownership_percent'))->required()->numeric()->default(100),
                    ]),
            ])
            ->actions([
                Tables\Actions\DetachAction::make(),
                Tables\Actions\EditAction::make(),
            ]);
    }
}
