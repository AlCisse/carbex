<?php

namespace App\Filament\Resources\EmissionFactorResource\Pages;

use App\Filament\Resources\EmissionFactorResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditEmissionFactor extends EditRecord
{
    protected static string $resource = EmissionFactorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
