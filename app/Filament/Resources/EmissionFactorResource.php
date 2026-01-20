<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EmissionFactorResource\Pages;
use App\Models\EmissionFactor;
use Filament\Actions\ViewAction;
use Filament\Actions\EditAction;
use Filament\Actions\ReplicateAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Schemas;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class EmissionFactorResource extends Resource
{
    protected static ?string $model = EmissionFactor::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-calculator';

    protected static string | \UnitEnum | null $navigationGroup = 'Carbon Data';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Schemas\Components\Section::make('Factor Information')
                    ->schema([
                        Schemas\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),

                        Schemas\Components\TextInput::make('name_en')
                            ->label('Name (English)')
                            ->maxLength(255),

                        Schemas\Components\Select::make('category')
                            ->options([
                                'electricity' => 'Electricity',
                                'natural_gas' => 'Natural Gas',
                                'fuel' => 'Fuel',
                                'business_travel' => 'Business Travel',
                                'freight' => 'Freight',
                                'purchased_goods' => 'Purchased Goods',
                                'waste' => 'Waste',
                            ])
                            ->required()
                            ->searchable(),

                        Schemas\Components\TextInput::make('subcategory')
                            ->maxLength(100),

                        Schemas\Components\Select::make('scope')
                            ->options([
                                1 => 'Scope 1 - Direct',
                                2 => 'Scope 2 - Energy',
                                3 => 'Scope 3 - Value Chain',
                            ])
                            ->required(),

                        Schemas\Components\TextInput::make('ghg_category')
                            ->label('GHG Protocol Category')
                            ->helperText('e.g., Cat. 1, Cat. 6')
                            ->maxLength(20),
                    ])
                    ->columns(2),

                Schemas\Components\Section::make('Value & Units')
                    ->schema([
                        Schemas\Components\TextInput::make('value')
                            ->required()
                            ->numeric()
                            ->step(0.000001),

                        Schemas\Components\TextInput::make('unit')
                            ->required()
                            ->maxLength(50)
                            ->helperText('e.g., kgCO2e/kWh, kgCO2e/km'),

                        Schemas\Components\TextInput::make('uncertainty')
                            ->numeric()
                            ->suffix('%')
                            ->helperText('Uncertainty percentage'),

                        Schemas\Components\Select::make('calculation_method')
                            ->options([
                                'location_based' => 'Location-based',
                                'market_based' => 'Market-based',
                                'activity_based' => 'Activity-based',
                                'spend_based' => 'Spend-based',
                            ]),
                    ])
                    ->columns(2),

                Schemas\Components\Section::make('Source & Metadata')
                    ->schema([
                        Schemas\Components\Select::make('country')
                            ->options([
                                'FR' => 'France',
                                'DE' => 'Germany',
                                'BE' => 'Belgium',
                                'NL' => 'Netherlands',
                                'AT' => 'Austria',
                                'CH' => 'Switzerland',
                                'ES' => 'Spain',
                                'IT' => 'Italy',
                                'GB' => 'United Kingdom',
                            ])
                            ->searchable()
                            ->placeholder('Global (all countries)'),

                        Schemas\Components\TextInput::make('year')
                            ->numeric()
                            ->minValue(2000)
                            ->maxValue(2050)
                            ->required(),

                        Schemas\Components\TextInput::make('source')
                            ->required()
                            ->maxLength(255)
                            ->helperText('e.g., ADEME, DEFRA, EPA'),

                        Schemas\Components\TextInput::make('source_url')
                            ->url()
                            ->maxLength(500),

                        Schemas\Components\Textarea::make('notes')
                            ->rows(3),

                        Schemas\Components\Toggle::make('is_active')
                            ->default(true),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->limit(40),

                Tables\Columns\TextColumn::make('sector')
                    ->badge()
                    ->sortable(),

                Tables\Columns\TextColumn::make('scope')
                    ->badge()
                    ->color(fn (int $state): string => match ($state) {
                        1 => 'success',
                        2 => 'info',
                        3 => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (int $state): string => "Scope {$state}")
                    ->sortable(),

                Tables\Columns\TextColumn::make('factor_kg_co2e')
                    ->label('Factor (kgCO2e)')
                    ->numeric(decimalPlaces: 4)
                    ->sortable(),

                Tables\Columns\TextColumn::make('unit')
                    ->sortable(),

                Tables\Columns\TextColumn::make('country')
                    ->badge()
                    ->placeholder('Global')
                    ->sortable(),

                Tables\Columns\TextColumn::make('source')
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('scope')
                    ->options([
                        1 => 'Scope 1',
                        2 => 'Scope 2',
                        3 => 'Scope 3',
                    ]),

                Tables\Filters\SelectFilter::make('sector')
                    ->options([
                        'electricity' => 'Electricity',
                        'natural_gas' => 'Natural Gas',
                        'fuel' => 'Fuel',
                        'business_travel' => 'Business Travel',
                    ]),

                Tables\Filters\SelectFilter::make('country')
                    ->options([
                        'FR' => 'France',
                        'DE' => 'Germany',
                    ]),

                Tables\Filters\SelectFilter::make('source')
                    ->options([
                        'ADEME' => 'ADEME',
                        'DEFRA' => 'DEFRA',
                        'UBA' => 'UBA',
                        'IEA' => 'IEA',
                    ]),

                Tables\Filters\TernaryFilter::make('is_active'),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                ReplicateAction::make(),
            ])
            ->bulkActions([
                DeleteBulkAction::make(),
            ])
            ->defaultSort('name');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEmissionFactors::route('/'),
            'create' => Pages\CreateEmissionFactor::route('/create'),
            'view' => Pages\ViewEmissionFactor::route('/{record}'),
            'edit' => Pages\EditEmissionFactor::route('/{record}/edit'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'name_en', 'sector', 'source'];
    }
}
