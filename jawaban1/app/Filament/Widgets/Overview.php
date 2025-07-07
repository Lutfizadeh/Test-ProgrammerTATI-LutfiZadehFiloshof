<?php

namespace App\Filament\Widgets;

use Illuminate\Support\Facades\Auth;
use Filament\Widgets\StatsOverviewWidget\Card;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class Overview extends BaseWidget
{
    protected function getStats(): array
    {
        if (Auth::user()?->hasRole('super_admin')) {
            return [
                Card::make('Total Users', fn() => \App\Models\User::count())
                    ->icon('heroicon-o-rectangle-stack')
                    ->color('success'),
                Card::make('Total Logs', fn() => \App\Models\Log::count())
                    ->icon('heroicon-o-rectangle-stack')
                    ->color('success'),
                Card::make('Total Verified Logs', fn() => \App\Models\Log::where('status', '!=', 'Pending')->count())
                    ->icon('heroicon-o-rectangle-stack')
                    ->color('success'),
            ];
        } else {
            return [
                Card::make('Total Logs', fn() => \App\Models\Log::where('user_id', Auth::id())->count())
                    ->icon('heroicon-o-rectangle-stack')
                    ->color('success'),
                Card::make('Total Verified Logs', fn() => \App\Models\Log::where('user_id', Auth::id())
                    ->where('status', '!=', 'Pending')->count())
                    ->icon('heroicon-o-rectangle-stack')
                    ->color('success'),
            ];
        }
    }
}
