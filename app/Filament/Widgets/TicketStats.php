<?php

namespace App\Filament\Widgets;

use App\Models\Ticket;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TicketStats extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Tickets', Ticket::count())
                ->description('All tickets in the system')
                ->descriptionIcon('heroicon-o-ticket')
                ->color('primary'),

            Stat::make('Pending Tickets', Ticket::where('status', 'pending')->count())
                ->description('Tickets awaiting action')
                ->descriptionIcon('heroicon-o-clock')
                ->color('warning'),

            Stat::make('In Progress', Ticket::where('status', 'in_progress')->count())
                ->description('Tickets currently being worked on')
                ->descriptionIcon('heroicon-o-arrow-path')
                ->color('info'),

            Stat::make('Completed', Ticket::where('status', 'completed')->count())
                ->description('Successfully resolved tickets')
                ->descriptionIcon('heroicon-o-check-circle')
                ->color('success'),
        ];
    }
}
