<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SubscriptionResource\Pages;
use App\Models\Subscription;
use Filament\Actions\ViewAction;
use Filament\Actions\EditAction;
use Filament\Actions\Action;
use Filament\Actions\DeleteBulkAction;
use Filament\Schemas;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class SubscriptionResource extends Resource
{
    protected static ?string $model = Subscription::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-credit-card';

    protected static string | \UnitEnum | null $navigationGroup = 'Finance';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Schemas\Components\Section::make(__('linscarbon.admin.subscriptions.general'))
                    ->schema([
                        Schemas\Components\Select::make('organization_id')
                            ->label(__('linscarbon.admin.subscriptions.organization'))
                            ->relationship('organization', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Schemas\Components\Select::make('plan')
                            ->label(__('linscarbon.admin.subscriptions.plan'))
                            ->options([
                                'starter' => __('linscarbon.admin.subscriptions.plans.starter'),
                                'professional' => __('linscarbon.admin.subscriptions.plans.professional'),
                                'enterprise' => __('linscarbon.admin.subscriptions.plans.enterprise'),
                            ])
                            ->required(),

                        Schemas\Components\Select::make('status')
                            ->label(__('linscarbon.admin.subscriptions.status'))
                            ->options([
                                'active' => __('linscarbon.admin.subscriptions.statuses.active'),
                                'trialing' => __('linscarbon.admin.subscriptions.statuses.trialing'),
                                'canceled' => __('linscarbon.admin.subscriptions.statuses.canceled'),
                                'paused' => __('linscarbon.admin.subscriptions.statuses.paused'),
                                'past_due' => __('linscarbon.admin.subscriptions.statuses.past_due'),
                                'incomplete' => __('linscarbon.admin.subscriptions.statuses.incomplete'),
                            ])
                            ->required(),

                        Schemas\Components\Select::make('billing_cycle')
                            ->label(__('linscarbon.admin.subscriptions.billing_cycle'))
                            ->options([
                                'monthly' => __('linscarbon.admin.subscriptions.cycles.monthly'),
                                'yearly' => __('linscarbon.admin.subscriptions.cycles.yearly'),
                            ]),
                    ])
                    ->columns(2),

                Schemas\Components\Section::make(__('linscarbon.admin.subscriptions.stripe'))
                    ->schema([
                        Schemas\Components\TextInput::make('stripe_subscription_id')
                            ->label(__('linscarbon.admin.subscriptions.stripe_subscription_id'))
                            ->maxLength(255),

                        Schemas\Components\TextInput::make('stripe_customer_id')
                            ->label(__('linscarbon.admin.subscriptions.stripe_customer_id'))
                            ->maxLength(255),

                        Schemas\Components\TextInput::make('stripe_price_id')
                            ->label(__('linscarbon.admin.subscriptions.stripe_price_id'))
                            ->maxLength(255),
                    ])
                    ->columns(3)
                    ->collapsible(),

                Schemas\Components\Section::make(__('linscarbon.admin.subscriptions.period'))
                    ->schema([
                        Schemas\Components\DateTimePicker::make('current_period_start')
                            ->label(__('linscarbon.admin.subscriptions.period_start')),

                        Schemas\Components\DateTimePicker::make('current_period_end')
                            ->label(__('linscarbon.admin.subscriptions.period_end')),

                        Schemas\Components\DateTimePicker::make('trial_ends_at')
                            ->label(__('linscarbon.admin.subscriptions.trial_ends_at')),

                        Schemas\Components\DateTimePicker::make('canceled_at')
                            ->label(__('linscarbon.admin.subscriptions.canceled_at')),

                        Schemas\Components\Toggle::make('cancel_at_period_end')
                            ->label(__('linscarbon.admin.subscriptions.cancel_at_period_end')),
                    ])
                    ->columns(3),

                Schemas\Components\Section::make(__('linscarbon.admin.subscriptions.limits'))
                    ->schema([
                        Schemas\Components\TextInput::make('bank_connections_limit')
                            ->label(__('linscarbon.admin.subscriptions.bank_connections_limit'))
                            ->numeric()
                            ->minValue(0),

                        Schemas\Components\TextInput::make('bank_connections_used')
                            ->label(__('linscarbon.admin.subscriptions.bank_connections_used'))
                            ->numeric()
                            ->minValue(0)
                            ->disabled(),

                        Schemas\Components\TextInput::make('users_limit')
                            ->label(__('linscarbon.admin.subscriptions.users_limit'))
                            ->numeric()
                            ->minValue(0),

                        Schemas\Components\TextInput::make('users_used')
                            ->label(__('linscarbon.admin.subscriptions.users_used'))
                            ->numeric()
                            ->minValue(0)
                            ->disabled(),

                        Schemas\Components\TextInput::make('sites_limit')
                            ->label(__('linscarbon.admin.subscriptions.sites_limit'))
                            ->numeric()
                            ->minValue(0),

                        Schemas\Components\TextInput::make('sites_used')
                            ->label(__('linscarbon.admin.subscriptions.sites_used'))
                            ->numeric()
                            ->minValue(0)
                            ->disabled(),

                        Schemas\Components\TextInput::make('reports_monthly_limit')
                            ->label(__('linscarbon.admin.subscriptions.reports_limit'))
                            ->numeric()
                            ->minValue(0),

                        Schemas\Components\TextInput::make('reports_monthly_used')
                            ->label(__('linscarbon.admin.subscriptions.reports_used'))
                            ->numeric()
                            ->minValue(0)
                            ->disabled(),
                    ])
                    ->columns(4),

                Schemas\Components\Section::make(__('linscarbon.admin.subscriptions.features_section'))
                    ->schema([
                        Schemas\Components\CheckboxList::make('features')
                            ->label(__('linscarbon.admin.subscriptions.features'))
                            ->options([
                                'banking_integration' => __('linscarbon.admin.subscriptions.feature_options.banking'),
                                'ai_categorization' => __('linscarbon.admin.subscriptions.feature_options.ai'),
                                'advanced_reports' => __('linscarbon.admin.subscriptions.feature_options.reports'),
                                'api_access' => __('linscarbon.admin.subscriptions.feature_options.api'),
                                'supplier_portal' => __('linscarbon.admin.subscriptions.feature_options.supplier'),
                                'csrd_compliance' => __('linscarbon.admin.subscriptions.feature_options.csrd'),
                                'white_label' => __('linscarbon.admin.subscriptions.feature_options.white_label'),
                                'priority_support' => __('linscarbon.admin.subscriptions.feature_options.support'),
                            ])
                            ->columns(4),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('organization.name')
                    ->label(__('linscarbon.admin.subscriptions.organization'))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('plan')
                    ->label(__('linscarbon.admin.subscriptions.plan'))
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'enterprise' => 'success',
                        'professional' => 'warning',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('status')
                    ->label(__('linscarbon.admin.subscriptions.status'))
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'trialing' => 'info',
                        'canceled' => 'danger',
                        'paused' => 'warning',
                        'past_due' => 'danger',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('billing_cycle')
                    ->label(__('linscarbon.admin.subscriptions.billing_cycle'))
                    ->badge(),

                Tables\Columns\TextColumn::make('current_period_end')
                    ->label(__('linscarbon.admin.subscriptions.period_end'))
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('trial_ends_at')
                    ->label(__('linscarbon.admin.subscriptions.trial_ends_at'))
                    ->date()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('users_used')
                    ->label(__('linscarbon.admin.subscriptions.users'))
                    ->formatStateUsing(fn (Subscription $record): string => "{$record->users_used}/{$record->users_limit}")
                    ->toggleable(),

                Tables\Columns\TextColumn::make('sites_used')
                    ->label(__('linscarbon.admin.subscriptions.sites'))
                    ->formatStateUsing(fn (Subscription $record): string => "{$record->sites_used}/{$record->sites_limit}")
                    ->toggleable(),

                Tables\Columns\IconColumn::make('cancel_at_period_end')
                    ->label(__('linscarbon.admin.subscriptions.canceling'))
                    ->boolean()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('linscarbon.admin.subscriptions.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('plan')
                    ->options([
                        'starter' => __('linscarbon.admin.subscriptions.plans.starter'),
                        'professional' => __('linscarbon.admin.subscriptions.plans.professional'),
                        'enterprise' => __('linscarbon.admin.subscriptions.plans.enterprise'),
                    ]),

                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'active' => __('linscarbon.admin.subscriptions.statuses.active'),
                        'trialing' => __('linscarbon.admin.subscriptions.statuses.trialing'),
                        'canceled' => __('linscarbon.admin.subscriptions.statuses.canceled'),
                        'paused' => __('linscarbon.admin.subscriptions.statuses.paused'),
                    ]),

                Tables\Filters\SelectFilter::make('billing_cycle')
                    ->options([
                        'monthly' => __('linscarbon.admin.subscriptions.cycles.monthly'),
                        'yearly' => __('linscarbon.admin.subscriptions.cycles.yearly'),
                    ]),

                Tables\Filters\TernaryFilter::make('cancel_at_period_end')
                    ->label(__('linscarbon.admin.subscriptions.canceling')),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                Action::make('cancel')
                    ->label(__('linscarbon.admin.subscriptions.cancel'))
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn (Subscription $record): bool => $record->status === 'active'),
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
            'index' => Pages\ListSubscriptions::route('/'),
            'create' => Pages\CreateSubscription::route('/create'),
            'view' => Pages\ViewSubscription::route('/{record}'),
            'edit' => Pages\EditSubscription::route('/{record}/edit'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['organization.name', 'stripe_subscription_id'];
    }

    public static function getNavigationLabel(): string
    {
        return __('linscarbon.admin.navigation.subscriptions');
    }

    public static function getModelLabel(): string
    {
        return __('linscarbon.admin.subscriptions.singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('linscarbon.admin.subscriptions.plural');
    }
}
