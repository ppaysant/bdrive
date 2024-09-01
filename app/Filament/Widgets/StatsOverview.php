<?php

namespace App\Filament\Widgets;

use App\Models\Album;
use App\Models\Author;
use App\Models\Publisher;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected static ?string $pollingInterval = null;

    protected function getStats(): array
    {
        return [
            Stat::make('Total albums', Album::count()),
            Stat::make('Total authors', Author::count()),
            Stat::make('Total publishers', Publisher::count()),
        ];
    }
}
