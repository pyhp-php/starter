<?php

declare(strict_types=1);

// A small web page with a pandas report and a matplotlib chart.
// Run: composer serve   (or: php -S localhost:8000 -t public), then open http://localhost:8000

require __DIR__.'/../vendor/autoload.php';

use App\RevenueChart;
use App\SalesReport;
use PyHP\Py;

$py = Py::session();
$report = new SalesReport($py, __DIR__.'/../data/orders.csv');
$monthly = $report->monthly();
$chart = (new RevenueChart($py))->monthlySvg($monthly);
$cities = $report->revenueByCity();
$total = $report->totalRevenue();
$orders = $report->orderCount();
$py->close();

$e = static fn (string $text): string => htmlspecialchars($text, ENT_QUOTES);
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sales report · PyHP starter</title>
    <style>
        body { font-family: system-ui, sans-serif; margin: 2rem auto; max-width: 52rem; padding: 0 1rem; color: #1d2733; }
        h1 { margin-bottom: .2rem; }
        .muted { color: #5b6b7c; margin-top: 0; }
        .cards { display: flex; gap: 1rem; margin: 1.5rem 0; }
        .card { flex: 1; background: #f3f6f9; border-radius: .6rem; padding: 1rem; }
        .card strong { display: block; font-size: 1.6rem; }
        svg { width: 100%; height: auto; }
        table { border-collapse: collapse; width: 100%; }
        th, td { text-align: left; padding: .45rem .6rem; border-bottom: 1px solid #e3e8ee; }
        th.number, td.number { text-align: right; font-variant-numeric: tabular-nums; }
    </style>
</head>
<body>
    <h1>Sales report</h1>
    <p class="muted">Computed with pandas, drawn with matplotlib, served by PHP through PyHP.</p>

    <div class="cards">
        <div class="card"><strong><?= number_format($total, 2) ?></strong>USD revenue</div>
        <div class="card"><strong><?= $orders ?></strong>orders</div>
        <div class="card"><strong><?= count($cities) ?></strong>cities</div>
    </div>

    <?= $chart ?>

    <h2>Revenue by city</h2>
    <table>
        <thead><tr><th>City</th><th class="number">Revenue (USD)</th></tr></thead>
        <tbody>
        <?php foreach ($cities as $city => $revenue): ?>
            <tr><td><?= $e($city) ?></td><td class="number"><?= number_format($revenue, 2) ?></td></tr>
        <?php endforeach ?>
        </tbody>
    </table>
</body>
</html>
