<?php

namespace App\Filament\Widgets;

use App\Models\Facility;
use App\Models\Package;
use App\Models\Subscription;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverviewWidget extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make(__('panel.dashboard.total_users'), User::where('type', 'user')->count())
                ->description(__('panel.dashboard.total_users_description'))
                ->descriptionIcon('heroicon-m-users')
                ->color('primary')
                ->chart([7, 3, 4, 5, 6, 3, 5]),

            Stat::make(__('panel.dashboard.total_packages'), Package::count())
                ->description(__('panel.dashboard.total_packages_description'))
                ->descriptionIcon('heroicon-m-cube')
                ->color('warning')
                ->chart([2, 3, 4, 5, 4, 3, 2]),

            Stat::make(__('panel.dashboard.total_subscriptions'), Subscription::count())
                ->description(__('panel.dashboard.total_subscriptions_description'))
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('info')
                ->chart([5, 4, 3, 5, 6, 7, 8]),

            Stat::make(__('panel.dashboard.total_facilities'), Facility::count())
                ->description(__('panel.dashboard.total_facilities_description'))
                ->descriptionIcon('heroicon-m-building-office')
                ->color('danger')
                ->chart([4, 5, 6, 5, 4, 6, 7]),
        ];
    }
}

