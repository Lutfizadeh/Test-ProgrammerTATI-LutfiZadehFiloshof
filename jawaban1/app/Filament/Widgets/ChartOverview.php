<?php

namespace App\Filament\Widgets;

use App\Models\Log;
use App\Models\User;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;

class ChartOverview extends ChartWidget
{
    protected static ?string $heading = 'Logs Overview';

    protected function getData(): array
    {
        $user = Auth::user();

        $userIds = \App\Models\User::where('atasan_id', $user->id)
            ->pluck('id')
            ->prepend($user->id);

        $totalLogs = Trend::model(Log::class)
            ->between(
                start: now()->subDays(7),
                end: now(),
            )
            ->perDay()
            ->count();

        $verifiedLogs = Trend::query(
            Log::query()->where('status', '!=', 'Pending')
        )->between(
            start: now()->subDays(7),
            end: now(),
        )->perDay()->count();

        return [
            'datasets' => [
                [
                    'label' => 'Total Logs',
                    'data' => $totalLogs->map(function (TrendValue $value) use ($user, $userIds) {
                        return $user->hasRole('super_admin')
                            ? $value->aggregate
                            : Log::whereDate('created_at', $value->date)
                            ->whereIn('user_id', $userIds)
                            ->count();
                    }),
                    'borderColor' => '#6366F1', // Indigo
                    'backgroundColor' => 'rgba(99, 102, 241, 0.2)',
                ],
                [
                    'label' => 'Total Verified Logs',
                    'data' => $verifiedLogs->map(function (TrendValue $value) use ($user, $userIds) {
                        return $user->hasRole('super_admin')
                            ? $value->aggregate
                            : Log::whereDate('created_at', $value->date)
                            ->whereIn('user_id', $userIds)
                            ->where('status', '!=', 'Pending')
                            ->count();
                    }),
                    'borderColor' => '#10B981', // Emerald
                    'backgroundColor' => 'rgba(16, 185, 129, 0.2)',
                ],
            ],
            'labels' => $totalLogs->map(fn(TrendValue $value) => $value->date),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
