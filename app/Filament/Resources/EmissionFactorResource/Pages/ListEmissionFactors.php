<?php

namespace App\Filament\Resources\EmissionFactorResource\Pages;

use App\Filament\Resources\EmissionFactorResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListEmissionFactors extends ListRecords
{
    protected static string $resource = EmissionFactorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
