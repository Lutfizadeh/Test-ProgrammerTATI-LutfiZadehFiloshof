<?php

namespace App\Filament\Admin\Resources\LogResource\Pages;

use App\Filament\Admin\Resources\LogResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageLogs extends ManageRecords
{
    protected static string $resource = LogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
