<?php

declare(strict_types=1);

use App\RevenueChart;
use PyHP\Py;

test('the monthly chart is an SVG document', function (): void {
    $py = Py::session();
    try {
        $svg = (new RevenueChart($py))->monthlySvg([
            ['month' => '2026-10', 'orders' => 3, 'revenue' => 120.0],
            ['month' => '2026-11', 'orders' => 5, 'revenue' => 260.5],
        ]);
    } finally {
        $py->close();
    }

    expect($svg)->toContain('<svg')
        ->and($svg)->toContain('Revenue per month');
});
