<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Hash;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-users';

    protected static string | \UnitEnum | null $navigationGroup = 'Administration';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Forms\Components\Section::make(__('carbex.admin.users.personal_info'))
                    ->schema([
                        Forms\Components\TextInput::make('first_name')
                            ->label(__('carbex.admin.users.first_name'))
                            ->maxLength(255),

                        Forms\Components\TextInput::make('last_name')
                            ->label(__('carbex.admin.users.last_name'))
                            ->maxLength(255),

                        Forms\Components\TextInput::make('name')
                            ->label(__('carbex.admin.users.display_name'))
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('email')
                            ->label(__('carbex.admin.users.email'))
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),

                        Forms\Components\TextInput::make('phone')
                            ->label(__('carbex.admin.users.phone'))
                            ->tel()
                            ->maxLength(20),

                        Forms\Components\TextInput::make('job_title')
                            ->label(__('carbex.admin.users.job_title'))
                            ->maxLength(255),

                        Forms\Components\TextInput::make('department')
                            ->label(__('carbex.admin.users.department'))
                            ->maxLength(255),
                    ])
                    ->columns(2),

                Forms\Components\Section::make(__('carbex.admin.users.organization_role'))
                    ->schema([
                        Forms\Components\Select::make('organization_id')
                            ->label(__('carbex.admin.users.organization'))
                            ->relationship('organization', 'name')
                            ->searchable()
                            ->preload(),

                        Forms\Components\Select::make('role')
                            ->label(__('carbex.admin.users.role'))
                            ->options([
                                'owner' => __('carbex.admin.users.roles.owner'),
                                'admin' => __('carbex.admin.users.roles.admin'),
                                'manager' => __('carbex.admin.users.roles.manager'),
                                'member' => __('carbex.admin.users.roles.member'),
                                'viewer' => __('carbex.admin.users.roles.viewer'),
                            ])
                            ->required(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make(__('carbex.admin.users.security'))
                    ->schema([
                        Forms\Components\TextInput::make('password')
                            ->label(__('carbex.admin.users.password'))
                            ->password()
                            ->dehydrateStateUsing(fn (string $state): string => Hash::make($state))
                            ->dehydrated(fn (?string $state): bool => filled($state))
                            ->required(fn (string $operation): bool => $operation === 'create'),

                        Forms\Components\Toggle::make('is_active')
                            ->label(__('carbex.admin.users.is_active'))
                            ->default(true),

                        Forms\Components\Toggle::make('two_factor_enabled')
                            ->label(__('carbex.admin.users.two_factor'))
                            ->disabled(),

                        Forms\Components\DateTimePicker::make('email_verified_at')
                            ->label(__('carbex.admin.users.email_verified_at')),
                    ])
                    ->columns(2),

                Forms\Components\Section::make(__('carbex.admin.users.preferences'))
                    ->schema([
                        Forms\Components\Select::make('locale')
                            ->label(__('carbex.admin.users.locale'))
                            ->options([
                                'de' => 'Deutsch',
                                'en' => 'English',
                                'fr' => 'Francais',
                            ])
                            ->default('de'),

                        Forms\Components\Select::make('timezone')
                            ->label(__('carbex.admin.users.timezone'))
                            ->options([
                                'Europe/Berlin' => 'Europe/Berlin',
                                'Europe/Paris' => 'Europe/Paris',
                                'Europe/London' => 'Europe/London',
                                'Europe/Amsterdam' => 'Europe/Amsterdam',
                                'Europe/Brussels' => 'Europe/Brussels',
                                'Europe/Vienna' => 'Europe/Vienna',
                                'Europe/Zurich' => 'Europe/Zurich',
                            ])
                            ->default('Europe/Berlin'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('carbex.admin.users.name'))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('email')
                    ->label(__('carbex.admin.users.email'))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('organization.name')
                    ->label(__('carbex.admin.users.organization'))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('role')
                    ->label(__('carbex.admin.users.role'))
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'owner' => 'danger',
                        'admin' => 'warning',
                        'manager' => 'success',
                        'member' => 'info',
                        default => 'gray',
                    }),

                Tables\Columns\IconColumn::make('is_active')
                    ->label(__('carbex.admin.users.is_active'))
                    ->boolean(),

                Tables\Columns\IconColumn::make('two_factor_enabled')
                    ->label(__('carbex.admin.users.two_factor'))
                    ->boolean(),

                Tables\Columns\TextColumn::make('last_login_at')
                    ->label(__('carbex.admin.users.last_login'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('carbex.admin.users.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('organization')
                    ->relationship('organization', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('role')
                    ->options([
                        'owner' => __('carbex.admin.users.roles.owner'),
                        'admin' => __('carbex.admin.users.roles.admin'),
                        'manager' => __('carbex.admin.users.roles.manager'),
                        'member' => __('carbex.admin.users.roles.member'),
                        'viewer' => __('carbex.admin.users.roles.viewer'),
                    ]),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label(__('carbex.admin.users.is_active')),

                Tables\Filters\TernaryFilter::make('two_factor_enabled')
                    ->label(__('carbex.admin.users.two_factor')),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('impersonate')
                    ->label(__('carbex.admin.users.impersonate'))
                    ->icon('heroicon-o-user')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->visible(fn (User $record): bool => $record->is_active),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'view' => Pages\ViewUser::route('/{record}'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'email', 'first_name', 'last_name'];
    }

    public static function getNavigationLabel(): string
    {
        return __('carbex.admin.navigation.users');
    }

    public static function getModelLabel(): string
    {
        return __('carbex.admin.users.singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('carbex.admin.users.plural');
    }
}
