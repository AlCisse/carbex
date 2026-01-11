<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EmissionFactorResource\Pages;
use App\Models\EmissionFactor;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class EmissionFactorResource extends Resource
{
    protected static ?string $model = EmissionFactor::class;

    protected static ?string $navigationIcon = 'heroicon-o-calculator';

    protected static ?string $navigationGroup = 'Carbon Data';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Factor Information')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('name_en')
                            ->label('Name (English)')
                            ->maxLength(255),

                        Forms\Components\Select::make('category')
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

                        Forms\Components\TextInput::make('subcategory')
                            ->maxLength(100),

                        Forms\Components\Select::make('scope')
                            ->options([
                                1 => 'Scope 1 - Direct',
                                2 => 'Scope 2 - Energy',
                                3 => 'Scope 3 - Value Chain',
                            ])
                            ->required(),

                        Forms\Components\TextInput::make('ghg_category')
                            ->label('GHG Protocol Category')
                            ->helperText('e.g., Cat. 1, Cat. 6')
                            ->maxLength(20),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Value & Units')
                    ->schema([
                        Forms\Components\TextInput::make('value')
                            ->required()
                            ->numeric()
                            ->step(0.000001),

                        Forms\Components\TextInput::make('unit')
                            ->required()
                            ->maxLength(50)
                            ->helperText('e.g., kgCO2e/kWh, kgCO2e/km'),

                        Forms\Components\TextInput::make('uncertainty')
                            ->numeric()
                            ->suffix('%')
                            ->helperText('Uncertainty percentage'),

                        Forms\Components\Select::make('calculation_method')
                            ->options([
                                'location_based' => 'Location-based',
                                'market_based' => 'Market-based',
                                'activity_based' => 'Activity-based',
                                'spend_based' => 'Spend-based',
                            ]),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Source & Metadata')
                    ->schema([
                        Forms\Components\Select::make('country')
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

                        Forms\Components\TextInput::make('year')
                            ->numeric()
                            ->minValue(2000)
                            ->maxValue(2050)
                            ->required(),

                        Forms\Components\TextInput::make('source')
                            ->required()
                            ->maxLength(255)
                            ->helperText('e.g., ADEME, DEFRA, EPA'),

                        Forms\Components\TextInput::make('source_url')
                            ->url()
                            ->maxLength(500),

                        Forms\Components\Textarea::make('notes')
                            ->rows(3),

                        Forms\Components\Toggle::make('is_active')
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

                Tables\Columns\TextColumn::make('category')
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

                Tables\Columns\TextColumn::make('value')
                    ->numeric(decimalPlaces: 4)
                    ->sortable(),

                Tables\Columns\TextColumn::make('unit')
                    ->sortable(),

                Tables\Columns\TextColumn::make('country')
                    ->badge()
                    ->placeholder('Global')
                    ->sortable(),

                Tables\Columns\TextColumn::make('year')
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

                Tables\Filters\SelectFilter::make('category')
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
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\ReplicateAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('category');
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
        return ['name', 'name_en', 'category', 'source'];
    }
}
