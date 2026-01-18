<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SubscriptionResource\Pages;
use App\Models\Subscription;
use Filament\Forms;
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
                Forms\Components\Section::make(__('carbex.admin.subscriptions.general'))
                    ->schema([
                        Forms\Components\Select::make('organization_id')
                            ->label(__('carbex.admin.subscriptions.organization'))
                            ->relationship('organization', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Forms\Components\Select::make('plan')
                            ->label(__('carbex.admin.subscriptions.plan'))
                            ->options([
                                'starter' => __('carbex.admin.subscriptions.plans.starter'),
                                'professional' => __('carbex.admin.subscriptions.plans.professional'),
                                'enterprise' => __('carbex.admin.subscriptions.plans.enterprise'),
                            ])
                            ->required(),

                        Forms\Components\Select::make('status')
                            ->label(__('carbex.admin.subscriptions.status'))
                            ->options([
                                'active' => __('carbex.admin.subscriptions.statuses.active'),
                                'trialing' => __('carbex.admin.subscriptions.statuses.trialing'),
                                'canceled' => __('carbex.admin.subscriptions.statuses.canceled'),
                                'paused' => __('carbex.admin.subscriptions.statuses.paused'),
                                'past_due' => __('carbex.admin.subscriptions.statuses.past_due'),
                                'incomplete' => __('carbex.admin.subscriptions.statuses.incomplete'),
                            ])
                            ->required(),

                        Forms\Components\Select::make('billing_cycle')
                            ->label(__('carbex.admin.subscriptions.billing_cycle'))
                            ->options([
                                'monthly' => __('carbex.admin.subscriptions.cycles.monthly'),
                                'yearly' => __('carbex.admin.subscriptions.cycles.yearly'),
                            ]),
                    ])
                    ->columns(2),

                Forms\Components\Section::make(__('carbex.admin.subscriptions.stripe'))
                    ->schema([
                        Forms\Components\TextInput::make('stripe_subscription_id')
                            ->label(__('carbex.admin.subscriptions.stripe_subscription_id'))
                            ->maxLength(255),

                        Forms\Components\TextInput::make('stripe_customer_id')
                            ->label(__('carbex.admin.subscriptions.stripe_customer_id'))
                            ->maxLength(255),

                        Forms\Components\TextInput::make('stripe_price_id')
                            ->label(__('carbex.admin.subscriptions.stripe_price_id'))
                            ->maxLength(255),
                    ])
                    ->columns(3)
                    ->collapsible(),

                Forms\Components\Section::make(__('carbex.admin.subscriptions.period'))
                    ->schema([
                        Forms\Components\DateTimePicker::make('current_period_start')
                            ->label(__('carbex.admin.subscriptions.period_start')),

                        Forms\Components\DateTimePicker::make('current_period_end')
                            ->label(__('carbex.admin.subscriptions.period_end')),

                        Forms\Components\DateTimePicker::make('trial_ends_at')
                            ->label(__('carbex.admin.subscriptions.trial_ends_at')),

                        Forms\Components\DateTimePicker::make('canceled_at')
                            ->label(__('carbex.admin.subscriptions.canceled_at')),

                        Forms\Components\Toggle::make('cancel_at_period_end')
                            ->label(__('carbex.admin.subscriptions.cancel_at_period_end')),
                    ])
                    ->columns(3),

                Forms\Components\Section::make(__('carbex.admin.subscriptions.limits'))
                    ->schema([
                        Forms\Components\TextInput::make('bank_connections_limit')
                            ->label(__('carbex.admin.subscriptions.bank_connections_limit'))
                            ->numeric()
                            ->minValue(0),

                        Forms\Components\TextInput::make('bank_connections_used')
                            ->label(__('carbex.admin.subscriptions.bank_connections_used'))
                            ->numeric()
                            ->minValue(0)
                            ->disabled(),

                        Forms\Components\TextInput::make('users_limit')
                            ->label(__('carbex.admin.subscriptions.users_limit'))
                            ->numeric()
                            ->minValue(0),

                        Forms\Components\TextInput::make('users_used')
                            ->label(__('carbex.admin.subscriptions.users_used'))
                            ->numeric()
                            ->minValue(0)
                            ->disabled(),

                        Forms\Components\TextInput::make('sites_limit')
                            ->label(__('carbex.admin.subscriptions.sites_limit'))
                            ->numeric()
                            ->minValue(0),

                        Forms\Components\TextInput::make('sites_used')
                            ->label(__('carbex.admin.subscriptions.sites_used'))
                            ->numeric()
                            ->minValue(0)
                            ->disabled(),

                        Forms\Components\TextInput::make('reports_monthly_limit')
                            ->label(__('carbex.admin.subscriptions.reports_limit'))
                            ->numeric()
                            ->minValue(0),

                        Forms\Components\TextInput::make('reports_monthly_used')
                            ->label(__('carbex.admin.subscriptions.reports_used'))
                            ->numeric()
                            ->minValue(0)
                            ->disabled(),
                    ])
                    ->columns(4),

                Forms\Components\Section::make(__('carbex.admin.subscriptions.features_section'))
                    ->schema([
                        Forms\Components\CheckboxList::make('features')
                            ->label(__('carbex.admin.subscriptions.features'))
                            ->options([
                                'banking_integration' => __('carbex.admin.subscriptions.feature_options.banking'),
                                'ai_categorization' => __('carbex.admin.subscriptions.feature_options.ai'),
                                'advanced_reports' => __('carbex.admin.subscriptions.feature_options.reports'),
                                'api_access' => __('carbex.admin.subscriptions.feature_options.api'),
                                'supplier_portal' => __('carbex.admin.subscriptions.feature_options.supplier'),
                                'csrd_compliance' => __('carbex.admin.subscriptions.feature_options.csrd'),
                                'white_label' => __('carbex.admin.subscriptions.feature_options.white_label'),
                                'priority_support' => __('carbex.admin.subscriptions.feature_options.support'),
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
                    ->label(__('carbex.admin.subscriptions.organization'))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('plan')
                    ->label(__('carbex.admin.subscriptions.plan'))
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'enterprise' => 'success',
                        'professional' => 'warning',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('status')
                    ->label(__('carbex.admin.subscriptions.status'))
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
                    ->label(__('carbex.admin.subscriptions.billing_cycle'))
                    ->badge(),

                Tables\Columns\TextColumn::make('current_period_end')
                    ->label(__('carbex.admin.subscriptions.period_end'))
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('trial_ends_at')
                    ->label(__('carbex.admin.subscriptions.trial_ends_at'))
                    ->date()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('users_used')
                    ->label(__('carbex.admin.subscriptions.users'))
                    ->formatStateUsing(fn (Subscription $record): string => "{$record->users_used}/{$record->users_limit}")
                    ->toggleable(),

                Tables\Columns\TextColumn::make('sites_used')
                    ->label(__('carbex.admin.subscriptions.sites'))
                    ->formatStateUsing(fn (Subscription $record): string => "{$record->sites_used}/{$record->sites_limit}")
                    ->toggleable(),

                Tables\Columns\IconColumn::make('cancel_at_period_end')
                    ->label(__('carbex.admin.subscriptions.canceling'))
                    ->boolean()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('carbex.admin.subscriptions.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('plan')
                    ->options([
                        'starter' => __('carbex.admin.subscriptions.plans.starter'),
                        'professional' => __('carbex.admin.subscriptions.plans.professional'),
                        'enterprise' => __('carbex.admin.subscriptions.plans.enterprise'),
                    ]),

                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'active' => __('carbex.admin.subscriptions.statuses.active'),
                        'trialing' => __('carbex.admin.subscriptions.statuses.trialing'),
                        'canceled' => __('carbex.admin.subscriptions.statuses.canceled'),
                        'paused' => __('carbex.admin.subscriptions.statuses.paused'),
                    ]),

                Tables\Filters\SelectFilter::make('billing_cycle')
                    ->options([
                        'monthly' => __('carbex.admin.subscriptions.cycles.monthly'),
                        'yearly' => __('carbex.admin.subscriptions.cycles.yearly'),
                    ]),

                Tables\Filters\TernaryFilter::make('cancel_at_period_end')
                    ->label(__('carbex.admin.subscriptions.canceling')),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('cancel')
                    ->label(__('carbex.admin.subscriptions.cancel'))
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn (Subscription $record): bool => $record->status === 'active'),
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
        return __('carbex.admin.navigation.subscriptions');
    }

    public static function getModelLabel(): string
    {
        return __('carbex.admin.subscriptions.singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('carbex.admin.subscriptions.plural');
    }
}
