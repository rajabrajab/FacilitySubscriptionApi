<?php

namespace App\Filament\Widgets;

use App\Models\Facility;
use Filament\Widgets\ChartWidget;

class FacilitiesByTypeChartWidget extends ChartWidget
{
    protected ?string $heading = null;

    protected static ?int $sort = 3;

    protected int | string | array $columnSpan = [
        'md' => 1,
        'xl' => 1,
    ];

    public function getHeading(): string
    {
        return __('panel.dashboard.facilities_by_type_chart_title');
    }

    protected function getData(): array
    {
        $data = Facility::with('type')
            ->selectRaw('facility_type_id, COUNT(*) as count')
            ->groupBy('facility_type_id')
            ->get();

        if ($data->isEmpty()) {
            return [
                'datasets' => [
                    [
                        'label' => __('panel.facilities'),
                        'data' => [],
                        'backgroundColor' => [],
                        'borderColor' => [],
                        'borderWidth' => 2,
                    ],
                ],
                'labels' => [],
            ];
        }

        $labels = $data->map(fn ($item) => $item->type ? $item->type->name : __('panel.dashboard.unknown'))->toArray();
        $counts = $data->pluck('count')->toArray();

        $colors = [
            'rgba(59, 130, 246, 0.8)',
            'rgba(16, 185, 129, 0.8)',
            'rgba(245, 158, 11, 0.8)',
            'rgba(239, 68, 68, 0.8)',
            'rgba(139, 92, 246, 0.8)',
            'rgba(236, 72, 153, 0.8)',
        ];

        return [
            'datasets' => [
                [
                    'label' => __('panel.facilities'),
                    'data' => $counts,
                    'backgroundColor' => array_slice($colors, 0, count($counts)),
                    'borderColor' => array_map(fn ($color) => str_replace('0.8', '1', $color), array_slice($colors, 0, count($counts))),
                    'borderWidth' => 2,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}

