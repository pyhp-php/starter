<?php

declare(strict_types=1);

use App\SalesReport;
use PyHP\Py;

beforeEach(function (): void {
    $this->py = Py::session();
    $this->report = new SalesReport($this->py, dirname(__DIR__, 2).'/data/orders.csv');
});

afterEach(function (): void {
    $this->py->close();
});

test('totals are computed by pandas and arrive as PHP numbers', function (): void {
    expect($this->report->orderCount())->toBe(18)
        ->and($this->report->totalRevenue())->toBe(804.75);
});

test('revenue by city is sorted from highest to lowest', function (): void {
    $cities = $this->report->revenueByCity();

    expect(array_key_first($cities))->toBe('Ramallah')
        ->and($cities['Ramallah'])->toBe(277.0)
        ->and(array_sum($cities))->toBe(804.75);
});

test('monthly rows are plain arrays, oldest month first', function (): void {
    $monthly = $this->report->monthly();

    expect($monthly)->toHaveCount(5)
        ->and($monthly[0])->toBe(['month' => '2026-07', 'orders' => 4, 'revenue' => 105.0]);
});

test('top products are ranked by units sold', function (): void {
    expect($this->report->topProducts(2))->toBe(['Olive soap' => 43, "Za'atar 250g" => 32]);
});
