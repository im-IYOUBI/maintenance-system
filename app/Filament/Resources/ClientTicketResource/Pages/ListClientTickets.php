<?php

namespace App\Filament\Resources\ClientTicketResource\Pages;

use App\Filament\Resources\ClientTicketResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListClientTickets extends ListRecords
{
    protected static string $resource = ClientTicketResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
