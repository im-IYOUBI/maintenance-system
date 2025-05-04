<?php

namespace App\Filament\Resources\ClientTicketResource\Pages;

use App\Filament\Resources\ClientTicketResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditClientTicket extends EditRecord
{
    protected static string $resource = ClientTicketResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
