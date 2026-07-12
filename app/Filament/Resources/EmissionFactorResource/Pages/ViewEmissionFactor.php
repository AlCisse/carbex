<?php

namespace App\Filament\Resources\EmissionFactorResource\Pages;

use App\Filament\Resources\EmissionFactorResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewEmissionFactor extends ViewRecord
{
    protected static string $resource = EmissionFactorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
