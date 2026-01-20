<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SiteResource\Pages;
use App\Models\Site;
use Filament\Actions\ViewAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Schemas;
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
                Schemas\Components\Section::make(__('linscarbon.admin.sites.general_info'))
                    ->schema([
                        Schemas\Components\Select::make('organization_id')
                            ->label(__('linscarbon.admin.sites.organization'))
                            ->relationship('organization', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Schemas\Components\TextInput::make('name')
                            ->label(__('linscarbon.admin.sites.name'))
                            ->required()
                            ->maxLength(255),

                        Schemas\Components\TextInput::make('code')
                            ->label(__('linscarbon.admin.sites.code'))
                            ->maxLength(50),

                        Schemas\Components\Textarea::make('description')
                            ->label(__('linscarbon.admin.sites.description'))
                            ->rows(2),

                        Schemas\Components\Select::make('type')
                            ->label(__('linscarbon.admin.sites.type'))
                            ->options([
                                'office' => __('linscarbon.admin.sites.types.office'),
                                'warehouse' => __('linscarbon.admin.sites.types.warehouse'),
                                'factory' => __('linscarbon.admin.sites.types.factory'),
                                'retail' => __('linscarbon.admin.sites.types.retail'),
                                'datacenter' => __('linscarbon.admin.sites.types.datacenter'),
                                'other' => __('linscarbon.admin.sites.types.other'),
                            ]),

                        Schemas\Components\Select::make('building_type')
                            ->label(__('linscarbon.admin.sites.building_type'))
                            ->options([
                                'office_modern' => __('linscarbon.sites.building_types.office_modern'),
                                'office_traditional' => __('linscarbon.sites.building_types.office_traditional'),
                                'warehouse_heated' => __('linscarbon.sites.building_types.warehouse_heated'),
                                'warehouse_unheated' => __('linscarbon.sites.building_types.warehouse_unheated'),
                                'retail_standalone' => __('linscarbon.sites.building_types.retail_standalone'),
                                'retail_mall' => __('linscarbon.sites.building_types.retail_mall'),
                                'factory_light' => __('linscarbon.sites.building_types.factory_light'),
                                'factory_heavy' => __('linscarbon.sites.building_types.factory_heavy'),
                                'datacenter' => __('linscarbon.sites.building_types.datacenter'),
                                'mixed_use' => __('linscarbon.sites.building_types.mixed_use'),
                                'other' => __('linscarbon.sites.building_types.other'),
                            ]),
                    ])
                    ->columns(2),

                Schemas\Components\Section::make(__('linscarbon.admin.sites.location'))
                    ->schema([
                        Schemas\Components\TextInput::make('address_line_1')
                            ->label(__('linscarbon.admin.sites.address_line_1'))
                            ->maxLength(255),

                        Schemas\Components\TextInput::make('address_line_2')
                            ->label(__('linscarbon.admin.sites.address_line_2'))
                            ->maxLength(255),

                        Schemas\Components\TextInput::make('city')
                            ->label(__('linscarbon.admin.sites.city'))
                            ->maxLength(100),

                        Schemas\Components\TextInput::make('postal_code')
                            ->label(__('linscarbon.admin.sites.postal_code'))
                            ->maxLength(20),

                        Schemas\Components\Select::make('country')
                            ->label(__('linscarbon.admin.sites.country'))
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

                        Schemas\Components\TextInput::make('latitude')
                            ->label(__('linscarbon.admin.sites.latitude'))
                            ->numeric(),

                        Schemas\Components\TextInput::make('longitude')
                            ->label(__('linscarbon.admin.sites.longitude'))
                            ->numeric(),
                    ])
                    ->columns(2),

                Schemas\Components\Section::make(__('linscarbon.admin.sites.characteristics'))
                    ->schema([
                        Schemas\Components\TextInput::make('floor_area_m2')
                            ->label(__('linscarbon.admin.sites.floor_area'))
                            ->numeric()
                            ->suffix('m2'),

                        Schemas\Components\TextInput::make('employee_count')
                            ->label(__('linscarbon.admin.sites.employee_count'))
                            ->numeric()
                            ->minValue(0),

                        Schemas\Components\TextInput::make('construction_year')
                            ->label(__('linscarbon.admin.sites.construction_year'))
                            ->numeric()
                            ->minValue(1800)
                            ->maxValue(date('Y')),

                        Schemas\Components\TextInput::make('occupancy_rate')
                            ->label(__('linscarbon.admin.sites.occupancy_rate'))
                            ->numeric()
                            ->suffix('%')
                            ->minValue(0)
                            ->maxValue(100),

                        Schemas\Components\Select::make('energy_rating')
                            ->label(__('linscarbon.admin.sites.energy_rating'))
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

                Schemas\Components\Section::make(__('linscarbon.admin.sites.energy'))
                    ->schema([
                        Schemas\Components\TextInput::make('electricity_provider')
                            ->label(__('linscarbon.admin.sites.electricity_provider'))
                            ->maxLength(255),

                        Schemas\Components\Toggle::make('renewable_energy')
                            ->label(__('linscarbon.admin.sites.renewable_energy')),

                        Schemas\Components\TextInput::make('renewable_percentage')
                            ->label(__('linscarbon.admin.sites.renewable_percentage'))
                            ->numeric()
                            ->suffix('%')
                            ->minValue(0)
                            ->maxValue(100),

                        Schemas\Components\TextInput::make('annual_energy_kwh')
                            ->label(__('linscarbon.admin.sites.annual_energy'))
                            ->numeric()
                            ->suffix('kWh'),

                        Schemas\Components\Select::make('heating_type')
                            ->label(__('linscarbon.admin.sites.heating_type'))
                            ->options([
                                'gas' => __('linscarbon.admin.sites.heating_types.gas'),
                                'oil' => __('linscarbon.admin.sites.heating_types.oil'),
                                'electric' => __('linscarbon.admin.sites.heating_types.electric'),
                                'heat_pump' => __('linscarbon.admin.sites.heating_types.heat_pump'),
                                'district' => __('linscarbon.admin.sites.heating_types.district'),
                                'none' => __('linscarbon.admin.sites.heating_types.none'),
                            ]),

                        Schemas\Components\Select::make('cooling_type')
                            ->label(__('linscarbon.admin.sites.cooling_type'))
                            ->options([
                                'ac' => __('linscarbon.admin.sites.cooling_types.ac'),
                                'heat_pump' => __('linscarbon.admin.sites.cooling_types.heat_pump'),
                                'passive' => __('linscarbon.admin.sites.cooling_types.passive'),
                                'none' => __('linscarbon.admin.sites.cooling_types.none'),
                            ]),
                    ])
                    ->columns(3),

                Schemas\Components\Section::make(__('linscarbon.admin.sites.status'))
                    ->schema([
                        Schemas\Components\Toggle::make('is_primary')
                            ->label(__('linscarbon.admin.sites.is_primary')),

                        Schemas\Components\Toggle::make('is_active')
                            ->label(__('linscarbon.admin.sites.is_active'))
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
                    ->label(__('linscarbon.admin.sites.name'))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('organization.name')
                    ->label(__('linscarbon.admin.sites.organization'))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('type')
                    ->label(__('linscarbon.admin.sites.type'))
                    ->badge(),

                Tables\Columns\TextColumn::make('city')
                    ->label(__('linscarbon.admin.sites.city'))
                    ->searchable(),

                Tables\Columns\TextColumn::make('country')
                    ->label(__('linscarbon.admin.sites.country'))
                    ->badge(),

                Tables\Columns\TextColumn::make('floor_area_m2')
                    ->label(__('linscarbon.admin.sites.floor_area'))
                    ->numeric()
                    ->suffix(' m2')
                    ->sortable(),

                Tables\Columns\TextColumn::make('employee_count')
                    ->label(__('linscarbon.admin.sites.employees'))
                    ->numeric()
                    ->sortable(),

                Tables\Columns\TextColumn::make('energy_rating')
                    ->label(__('linscarbon.admin.sites.energy_rating'))
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
                    ->label(__('linscarbon.admin.sites.is_primary'))
                    ->boolean(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label(__('linscarbon.admin.sites.is_active'))
                    ->boolean(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('linscarbon.admin.sites.created_at'))
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
                        'office' => __('linscarbon.admin.sites.types.office'),
                        'warehouse' => __('linscarbon.admin.sites.types.warehouse'),
                        'factory' => __('linscarbon.admin.sites.types.factory'),
                        'retail' => __('linscarbon.admin.sites.types.retail'),
                        'datacenter' => __('linscarbon.admin.sites.types.datacenter'),
                    ]),

                Tables\Filters\SelectFilter::make('country')
                    ->options([
                        'DE' => 'Germany',
                        'FR' => 'France',
                        'BE' => 'Belgium',
                        'NL' => 'Netherlands',
                    ]),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label(__('linscarbon.admin.sites.is_active')),

                Tables\Filters\TernaryFilter::make('is_primary')
                    ->label(__('linscarbon.admin.sites.is_primary')),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->bulkActions([
                DeleteBulkAction::make(),
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
        return __('linscarbon.admin.navigation.sites');
    }

    public static function getModelLabel(): string
    {
        return __('linscarbon.admin.sites.singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('linscarbon.admin.sites.plural');
    }
}
