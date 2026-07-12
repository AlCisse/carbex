<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TransactionResource\Pages;
use App\Models\Transaction;
use Filament\Actions\ViewAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms;
use Filament\Schemas;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class TransactionResource extends Resource
{
    protected static ?string $model = Transaction::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-banknotes';

    protected static string | \UnitEnum | null $navigationGroup = 'Finance';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Schemas\Components\Section::make(__('linscarbon.admin.transactions.general'))
                    ->schema([
                        Forms\Components\Select::make('organization_id')
                            ->label(__('linscarbon.admin.transactions.organization'))
                            ->relationship('organization', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Forms\Components\Select::make('bank_account_id')
                            ->label(__('linscarbon.admin.transactions.bank_account'))
                            ->relationship('bankAccount', 'name')
                            ->searchable()
                            ->preload(),

                        Forms\Components\DatePicker::make('date')
                            ->label(__('linscarbon.admin.transactions.date'))
                            ->required(),

                        Forms\Components\DatePicker::make('value_date')
                            ->label(__('linscarbon.admin.transactions.value_date')),
                    ])
                    ->columns(2),

                Schemas\Components\Section::make(__('linscarbon.admin.transactions.amount_section'))
                    ->schema([
                        Forms\Components\TextInput::make('amount')
                            ->label(__('linscarbon.admin.transactions.amount'))
                            ->required()
                            ->numeric()
                            ->prefix('EUR'),

                        Forms\Components\Select::make('currency')
                            ->label(__('linscarbon.admin.transactions.currency'))
                            ->options([
                                'EUR' => 'EUR',
                                'USD' => 'USD',
                                'GBP' => 'GBP',
                                'CHF' => 'CHF',
                            ])
                            ->default('EUR'),

                        Forms\Components\TextInput::make('original_amount')
                            ->label(__('linscarbon.admin.transactions.original_amount'))
                            ->numeric(),

                        Forms\Components\Select::make('original_currency')
                            ->label(__('linscarbon.admin.transactions.original_currency'))
                            ->options([
                                'EUR' => 'EUR',
                                'USD' => 'USD',
                                'GBP' => 'GBP',
                                'CHF' => 'CHF',
                            ]),

                        Forms\Components\Select::make('type')
                            ->label(__('linscarbon.admin.transactions.type'))
                            ->options([
                                'debit' => __('linscarbon.admin.transactions.types.debit'),
                                'credit' => __('linscarbon.admin.transactions.types.credit'),
                                'transfer' => __('linscarbon.admin.transactions.types.transfer'),
                            ]),

                        Forms\Components\Select::make('status')
                            ->label(__('linscarbon.admin.transactions.status'))
                            ->options([
                                'pending' => __('linscarbon.admin.transactions.statuses.pending'),
                                'processed' => __('linscarbon.admin.transactions.statuses.processed'),
                                'validated' => __('linscarbon.admin.transactions.statuses.validated'),
                                'excluded' => __('linscarbon.admin.transactions.statuses.excluded'),
                            ])
                            ->required(),
                    ])
                    ->columns(3),

                Schemas\Components\Section::make(__('linscarbon.admin.transactions.description_section'))
                    ->schema([
                        Forms\Components\Textarea::make('description')
                            ->label(__('linscarbon.admin.transactions.description'))
                            ->rows(2),

                        Forms\Components\TextInput::make('clean_description')
                            ->label(__('linscarbon.admin.transactions.clean_description'))
                            ->maxLength(500),

                        Forms\Components\TextInput::make('counterparty_name')
                            ->label(__('linscarbon.admin.transactions.counterparty_name'))
                            ->maxLength(255),

                        Forms\Components\TextInput::make('counterparty_iban')
                            ->label(__('linscarbon.admin.transactions.counterparty_iban'))
                            ->maxLength(34),

                        Forms\Components\TextInput::make('mcc_code')
                            ->label(__('linscarbon.admin.transactions.mcc_code'))
                            ->maxLength(4),

                        Forms\Components\TextInput::make('merchant_category')
                            ->label(__('linscarbon.admin.transactions.merchant_category'))
                            ->maxLength(100),
                    ])
                    ->columns(2),

                Schemas\Components\Section::make(__('linscarbon.admin.transactions.categorization'))
                    ->schema([
                        Forms\Components\Select::make('category_id')
                            ->label(__('linscarbon.admin.transactions.category'))
                            ->relationship('category', 'name')
                            ->searchable()
                            ->preload(),

                        Forms\Components\Select::make('ai_category_id')
                            ->label(__('linscarbon.admin.transactions.ai_category'))
                            ->relationship('aiCategory', 'name')
                            ->searchable()
                            ->preload()
                            ->disabled(),

                        Forms\Components\TextInput::make('ai_confidence')
                            ->label(__('linscarbon.admin.transactions.ai_confidence'))
                            ->numeric()
                            ->suffix('%')
                            ->disabled(),

                        Forms\Components\Textarea::make('ai_reasoning')
                            ->label(__('linscarbon.admin.transactions.ai_reasoning'))
                            ->rows(2)
                            ->disabled(),

                        Forms\Components\Select::make('user_category_id')
                            ->label(__('linscarbon.admin.transactions.user_category'))
                            ->relationship('userCategory', 'name')
                            ->searchable()
                            ->preload(),
                    ])
                    ->columns(2),

                Schemas\Components\Section::make(__('linscarbon.admin.transactions.flags'))
                    ->schema([
                        Forms\Components\Toggle::make('is_excluded')
                            ->label(__('linscarbon.admin.transactions.is_excluded')),

                        Forms\Components\TextInput::make('exclusion_reason')
                            ->label(__('linscarbon.admin.transactions.exclusion_reason'))
                            ->maxLength(255),

                        Forms\Components\Toggle::make('is_recurring')
                            ->label(__('linscarbon.admin.transactions.is_recurring')),

                        Forms\Components\Select::make('validated_by')
                            ->label(__('linscarbon.admin.transactions.validated_by'))
                            ->relationship('validatedBy', 'name')
                            ->searchable()
                            ->preload(),

                        Forms\Components\DateTimePicker::make('validated_at')
                            ->label(__('linscarbon.admin.transactions.validated_at')),
                    ])
                    ->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('date')
                    ->label(__('linscarbon.admin.transactions.date'))
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('organization.name')
                    ->label(__('linscarbon.admin.transactions.organization'))
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('clean_description')
                    ->label(__('linscarbon.admin.transactions.description'))
                    ->limit(40)
                    ->searchable(),

                Tables\Columns\TextColumn::make('counterparty_name')
                    ->label(__('linscarbon.admin.transactions.counterparty'))
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('amount')
                    ->label(__('linscarbon.admin.transactions.amount'))
                    ->money('EUR')
                    ->sortable()
                    ->color(fn (Transaction $record): string => $record->amount < 0 ? 'danger' : 'success'),

                Tables\Columns\TextColumn::make('category.name')
                    ->label(__('linscarbon.admin.transactions.category'))
                    ->badge()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('ai_confidence')
                    ->label(__('linscarbon.admin.transactions.ai_confidence'))
                    ->formatStateUsing(fn (?float $state): string => $state ? number_format($state * 100, 0) . '%' : '-')
                    ->color(fn (?float $state): string => match (true) {
                        $state === null => 'gray',
                        $state >= 0.8 => 'success',
                        $state >= 0.5 => 'warning',
                        default => 'danger',
                    })
                    ->toggleable(),

                Tables\Columns\TextColumn::make('status')
                    ->label(__('linscarbon.admin.transactions.status'))
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'validated' => 'success',
                        'processed' => 'info',
                        'pending' => 'warning',
                        'excluded' => 'gray',
                        default => 'gray',
                    }),

                Tables\Columns\IconColumn::make('is_excluded')
                    ->label(__('linscarbon.admin.transactions.excluded'))
                    ->boolean()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('linscarbon.admin.transactions.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('organization')
                    ->relationship('organization', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('category')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => __('linscarbon.admin.transactions.statuses.pending'),
                        'processed' => __('linscarbon.admin.transactions.statuses.processed'),
                        'validated' => __('linscarbon.admin.transactions.statuses.validated'),
                        'excluded' => __('linscarbon.admin.transactions.statuses.excluded'),
                    ]),

                Tables\Filters\TernaryFilter::make('is_excluded')
                    ->label(__('linscarbon.admin.transactions.excluded')),

                Tables\Filters\TernaryFilter::make('is_recurring')
                    ->label(__('linscarbon.admin.transactions.recurring')),

                Tables\Filters\Filter::make('needs_review')
                    ->label(__('linscarbon.admin.transactions.needs_review'))
                    ->query(fn ($query) => $query->needsReview()),

                Tables\Filters\Filter::make('expenses')
                    ->label(__('linscarbon.admin.transactions.expenses'))
                    ->query(fn ($query) => $query->where('amount', '<', 0)),

                Tables\Filters\Filter::make('income')
                    ->label(__('linscarbon.admin.transactions.income'))
                    ->query(fn ($query) => $query->where('amount', '>', 0)),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->bulkActions([
                DeleteBulkAction::make(),
            ])
            ->defaultSort('date', 'desc');
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
            'index' => Pages\ListTransactions::route('/'),
            'create' => Pages\CreateTransaction::route('/create'),
            'view' => Pages\ViewTransaction::route('/{record}'),
            'edit' => Pages\EditTransaction::route('/{record}/edit'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['description', 'clean_description', 'merchant_name'];
    }

    public static function getNavigationLabel(): string
    {
        return __('linscarbon.admin.navigation.transactions');
    }

    public static function getModelLabel(): string
    {
        return __('linscarbon.admin.transactions.singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('linscarbon.admin.transactions.plural');
    }
}
