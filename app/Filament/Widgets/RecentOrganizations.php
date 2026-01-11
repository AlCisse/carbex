<?php

namespace App\Filament\Widgets;

use App\Models\Organization;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RecentOrganizations extends BaseWidget
{
    protected static ?int $sort = 4;

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Organization::query()
                    ->withCount(['users', 'sites'])
                    ->latest()
                    ->limit(10)
            )
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('country')
                    ->badge(),

                Tables\Columns\TextColumn::make('sector')
                    ->sortable(),

                Tables\Columns\TextColumn::make('users_count')
                    ->label('Users')
                    ->sortable(),

                Tables\Columns\TextColumn::make('sites_count')
                    ->label('Sites')
                    ->sortable(),

                Tables\Columns\TextColumn::make('plan')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'enterprise' => 'success',
                        'professional' => 'warning',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Joined')
                    ->since()
                    ->sortable(),
            ])
            ->paginated(false);
    }
}
