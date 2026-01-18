<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SiteResource\Pages;
use App\Models\Site;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class SiteResource extends Resource
{
    protected static ?string $model = Site::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-building-office-2';

    protected static string | \UnitEnum | null $navigationGroup = 'Administration';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Forms\Components\Section::make(__('carbex.admin.sites.general_info'))
                    ->schema([
                        Forms\Components\Select::make('organization_id')
                            ->label(__('carbex.admin.sites.organization'))
                            ->relationship('organization', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Forms\Components\TextInput::make('name')
                            ->label(__('carbex.admin.sites.name'))
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('code')
                            ->label(__('carbex.admin.sites.code'))
                            ->maxLength(50),

                        Forms\Components\Textarea::make('description')
                            ->label(__('carbex.admin.sites.description'))
                            ->rows(2),

                        Forms\Components\Select::make('type')
                            ->label(__('carbex.admin.sites.type'))
                            ->options([
                                'office' => __('carbex.admin.sites.types.office'),
                                'warehouse' => __('carbex.admin.sites.types.warehouse'),
                                'factory' => __('carbex.admin.sites.types.factory'),
                                'retail' => __('carbex.admin.sites.types.retail'),
                                'datacenter' => __('carbex.admin.sites.types.datacenter'),
                                'other' => __('carbex.admin.sites.types.other'),
                            ]),

                        Forms\Components\Select::make('building_type')
                            ->label(__('carbex.admin.sites.building_type'))
                            ->options([
                                'office_modern' => __('carbex.sites.building_types.office_modern'),
                                'office_traditional' => __('carbex.sites.building_types.office_traditional'),
                                'warehouse_heated' => __('carbex.sites.building_types.warehouse_heated'),
                                'warehouse_unheated' => __('carbex.sites.building_types.warehouse_unheated'),
                                'retail_standalone' => __('carbex.sites.building_types.retail_standalone'),
                                'retail_mall' => __('carbex.sites.building_types.retail_mall'),
                                'factory_light' => __('carbex.sites.building_types.factory_light'),
                                'factory_heavy' => __('carbex.sites.building_types.factory_heavy'),
                                'datacenter' => __('carbex.sites.building_types.datacenter'),
                                'mixed_use' => __('carbex.sites.building_types.mixed_use'),
                                'other' => __('carbex.sites.building_types.other'),
                            ]),
                    ])
                    ->columns(2),

                Forms\Components\Section::make(__('carbex.admin.sites.location'))
                    ->schema([
                        Forms\Components\TextInput::make('address_line_1')
                            ->label(__('carbex.admin.sites.address_line_1'))
                            ->maxLength(255),

                        Forms\Components\TextInput::make('address_line_2')
                            ->label(__('carbex.admin.sites.address_line_2'))
                            ->maxLength(255),

                        Forms\Components\TextInput::make('city')
                            ->label(__('carbex.admin.sites.city'))
                            ->maxLength(100),

                        Forms\Components\TextInput::make('postal_code')
                            ->label(__('carbex.admin.sites.postal_code'))
                            ->maxLength(20),

                        Forms\Components\Select::make('country')
                            ->label(__('carbex.admin.sites.country'))
                            ->options([
                                'DE' => 'Germany',
                                'FR' => 'France',
                                'BE' => 'Belgium',
                                'NL' => 'Netherlands',
                                'AT' => 'Austria',
                                'CH' => 'Switzerland',
                                'ES' => 'Spain',
                                'IT' => 'Italy',
                            ]),

                        Forms\Components\TextInput::make('latitude')
                            ->label(__('carbex.admin.sites.latitude'))
                            ->numeric(),

                        Forms\Components\TextInput::make('longitude')
                            ->label(__('carbex.admin.sites.longitude'))
                            ->numeric(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make(__('carbex.admin.sites.characteristics'))
                    ->schema([
                        Forms\Components\TextInput::make('floor_area_m2')
                            ->label(__('carbex.admin.sites.floor_area'))
                            ->numeric()
                            ->suffix('m2'),

                        Forms\Components\TextInput::make('employee_count')
                            ->label(__('carbex.admin.sites.employee_count'))
                            ->numeric()
                            ->minValue(0),

                        Forms\Components\TextInput::make('construction_year')
                            ->label(__('carbex.admin.sites.construction_year'))
                            ->numeric()
                            ->minValue(1800)
                            ->maxValue(date('Y')),

                        Forms\Components\TextInput::make('occupancy_rate')
                            ->label(__('carbex.admin.sites.occupancy_rate'))
                            ->numeric()
                            ->suffix('%')
                            ->minValue(0)
                            ->maxValue(100),

                        Forms\Components\Select::make('energy_rating')
                            ->label(__('carbex.admin.sites.energy_rating'))
                            ->options([
                                'A' => 'A',
                                'B' => 'B',
                                'C' => 'C',
                                'D' => 'D',
                                'E' => 'E',
                                'F' => 'F',
                                'G' => 'G',
                            ]),
                    ])
                    ->columns(3),

                Forms\Components\Section::make(__('carbex.admin.sites.energy'))
                    ->schema([
                        Forms\Components\TextInput::make('electricity_provider')
                            ->label(__('carbex.admin.sites.electricity_provider'))
                            ->maxLength(255),

                        Forms\Components\Toggle::make('renewable_energy')
                            ->label(__('carbex.admin.sites.renewable_energy')),

                        Forms\Components\TextInput::make('renewable_percentage')
                            ->label(__('carbex.admin.sites.renewable_percentage'))
                            ->numeric()
                            ->suffix('%')
                            ->minValue(0)
                            ->maxValue(100),

                        Forms\Components\TextInput::make('annual_energy_kwh')
                            ->label(__('carbex.admin.sites.annual_energy'))
                            ->numeric()
                            ->suffix('kWh'),

                        Forms\Components\Select::make('heating_type')
                            ->label(__('carbex.admin.sites.heating_type'))
                            ->options([
                                'gas' => __('carbex.admin.sites.heating_types.gas'),
                                'oil' => __('carbex.admin.sites.heating_types.oil'),
                                'electric' => __('carbex.admin.sites.heating_types.electric'),
                                'heat_pump' => __('carbex.admin.sites.heating_types.heat_pump'),
                                'district' => __('carbex.admin.sites.heating_types.district'),
                                'none' => __('carbex.admin.sites.heating_types.none'),
                            ]),

                        Forms\Components\Select::make('cooling_type')
                            ->label(__('carbex.admin.sites.cooling_type'))
                            ->options([
                                'ac' => __('carbex.admin.sites.cooling_types.ac'),
                                'heat_pump' => __('carbex.admin.sites.cooling_types.heat_pump'),
                                'passive' => __('carbex.admin.sites.cooling_types.passive'),
                                'none' => __('carbex.admin.sites.cooling_types.none'),
                            ]),
                    ])
                    ->columns(3),

                Forms\Components\Section::make(__('carbex.admin.sites.status'))
                    ->schema([
                        Forms\Components\Toggle::make('is_primary')
                            ->label(__('carbex.admin.sites.is_primary')),

                        Forms\Components\Toggle::make('is_active')
                            ->label(__('carbex.admin.sites.is_active'))
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
                    ->label(__('carbex.admin.sites.name'))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('organization.name')
                    ->label(__('carbex.admin.sites.organization'))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('type')
                    ->label(__('carbex.admin.sites.type'))
                    ->badge(),

                Tables\Columns\TextColumn::make('city')
                    ->label(__('carbex.admin.sites.city'))
                    ->searchable(),

                Tables\Columns\TextColumn::make('country')
                    ->label(__('carbex.admin.sites.country'))
                    ->badge(),

                Tables\Columns\TextColumn::make('floor_area_m2')
                    ->label(__('carbex.admin.sites.floor_area'))
                    ->numeric()
                    ->suffix(' m2')
                    ->sortable(),

                Tables\Columns\TextColumn::make('employee_count')
                    ->label(__('carbex.admin.sites.employees'))
                    ->numeric()
                    ->sortable(),

                Tables\Columns\TextColumn::make('energy_rating')
                    ->label(__('carbex.admin.sites.energy_rating'))
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'A' => 'success',
                        'B' => 'success',
                        'C' => 'warning',
                        'D' => 'warning',
                        'E' => 'danger',
                        'F' => 'danger',
                        'G' => 'danger',
                        default => 'gray',
                    }),

                Tables\Columns\IconColumn::make('is_primary')
                    ->label(__('carbex.admin.sites.is_primary'))
                    ->boolean(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label(__('carbex.admin.sites.is_active'))
                    ->boolean(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('carbex.admin.sites.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('organization')
                    ->relationship('organization', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'office' => __('carbex.admin.sites.types.office'),
                        'warehouse' => __('carbex.admin.sites.types.warehouse'),
                        'factory' => __('carbex.admin.sites.types.factory'),
                        'retail' => __('carbex.admin.sites.types.retail'),
                        'datacenter' => __('carbex.admin.sites.types.datacenter'),
                    ]),

                Tables\Filters\SelectFilter::make('country')
                    ->options([
                        'DE' => 'Germany',
                        'FR' => 'France',
                        'BE' => 'Belgium',
                        'NL' => 'Netherlands',
                    ]),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label(__('carbex.admin.sites.is_active')),

                Tables\Filters\TernaryFilter::make('is_primary')
                    ->label(__('carbex.admin.sites.is_primary')),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSites::route('/'),
            'create' => Pages\CreateSite::route('/create'),
            'view' => Pages\ViewSite::route('/{record}'),
            'edit' => Pages\EditSite::route('/{record}/edit'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'code', 'city', 'address_line_1'];
    }

    public static function getNavigationLabel(): string
    {
        return __('carbex.admin.navigation.sites');
    }

    public static function getModelLabel(): string
    {
        return __('carbex.admin.sites.singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('carbex.admin.sites.plural');
    }
}
