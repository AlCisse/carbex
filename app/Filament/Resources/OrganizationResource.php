<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrganizationResource\Pages;
use App\Models\Organization;
use Filament\Actions\ViewAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Schemas;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class OrganizationResource extends Resource
{
    protected static ?string $model = Organization::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-building-office';

    protected static string | \UnitEnum | null $navigationGroup = 'Administration';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Schemas\Components\Section::make('Organization Details')
                    ->schema([
                        Schemas\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),

                        Schemas\Components\TextInput::make('legal_name')
                            ->maxLength(255),

                        Schemas\Components\TextInput::make('siren')
                            ->label('SIREN')
                            ->maxLength(9),

                        Schemas\Components\TextInput::make('siret')
                            ->label('SIRET')
                            ->maxLength(14),

                        Schemas\Components\TextInput::make('vat_number')
                            ->label('VAT Number')
                            ->maxLength(20),

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
                            ])
                            ->required(),

                        Schemas\Components\Select::make('sector')
                            ->options([
                                'services' => 'Services',
                                'manufacturing' => 'Manufacturing',
                                'retail' => 'Retail',
                                'technology' => 'Technology',
                                'healthcare' => 'Healthcare',
                                'finance' => 'Finance',
                                'construction' => 'Construction',
                                'transport' => 'Transport',
                                'hospitality' => 'Hospitality',
                                'other' => 'Other',
                            ]),

                        Schemas\Components\TextInput::make('employees_count')
                            ->numeric()
                            ->minValue(1),

                        Schemas\Components\TextInput::make('annual_revenue')
                            ->numeric()
                            ->prefix('€'),
                    ])
                    ->columns(2),

                Schemas\Components\Section::make('Subscription')
                    ->schema([
                        Schemas\Components\Select::make('plan')
                            ->options([
                                'starter' => 'Starter',
                                'professional' => 'Professional',
                                'enterprise' => 'Enterprise',
                            ]),

                        Schemas\Components\Toggle::make('is_trial')
                            ->label('On Trial'),

                        Schemas\Components\DatePicker::make('trial_ends_at'),
                    ])
                    ->columns(3),

                Schemas\Components\Section::make('Settings')
                    ->schema([
                        Schemas\Components\Toggle::make('is_active')
                            ->label('Active')
                            ->default(true),

                        Schemas\Components\KeyValue::make('settings')
                            ->label('Custom Settings'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('country')
                    ->badge()
                    ->sortable(),

                Tables\Columns\TextColumn::make('sector')
                    ->sortable(),

                Tables\Columns\TextColumn::make('employees_count')
                    ->label('Employees')
                    ->numeric()
                    ->sortable(),

                Tables\Columns\TextColumn::make('users_count')
                    ->label('Users')
                    ->counts('users')
                    ->sortable(),

                Tables\Columns\TextColumn::make('sites_count')
                    ->label('Sites')
                    ->counts('sites')
                    ->sortable(),

                Tables\Columns\TextColumn::make('plan')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'enterprise' => 'success',
                        'professional' => 'warning',
                        default => 'gray',
                    }),

                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('country')
                    ->options([
                        'FR' => 'France',
                        'DE' => 'Germany',
                        'BE' => 'Belgium',
                        'NL' => 'Netherlands',
                    ]),

                Tables\Filters\SelectFilter::make('plan')
                    ->options([
                        'starter' => 'Starter',
                        'professional' => 'Professional',
                        'enterprise' => 'Enterprise',
                    ]),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active'),
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
            // RelationManagers\UsersRelationManager::class,
            // RelationManagers\SitesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrganizations::route('/'),
            'create' => Pages\CreateOrganization::route('/create'),
            'view' => Pages\ViewOrganization::route('/{record}'),
            'edit' => Pages\EditOrganization::route('/{record}/edit'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'legal_name', 'siren'];
    }
}
