<?php

namespace App\Filament\Resources\Orders\OrderResource\Pages;

use App\Filament\Resources\Orders\OrderResource;
use Filament\Actions; // Gunakan grup Actions untuk Header
use Filament\Resources\Pages\ViewRecord;

class ViewOrder extends ViewRecord
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Gunakan Actions\EditAction agar tidak tertukar dengan Tables\Actions
            Actions\EditAction::make(),
        ];
    }
}