<?php

declare(strict_types=1);

namespace App;

use PyHP\Py;
use PyHP\Session;

/** Draws charts with matplotlib and returns them as SVG, ready to put in a web page. */
final class RevenueChart
{
    public function __construct(private readonly Session $py)
    {
        // Servers have no screen: choose the "Agg" backend before pyplot is imported.
        $py->import('matplotlib')->use('Agg');
    }

    /** @param list<array{month: string, orders: int, revenue: float}> $monthly */
    public function monthlySvg(array $monthly): string
    {
        $plt = $this->py->import('matplotlib.pyplot', as: 'plt');
        [$figure, $axes] = $plt->subplots(figsize: Py::tuple(8, 3.6));
        $axes->bar(array_column($monthly, 'month'), array_column($monthly, 'revenue'), color: '#2E6F95');
        $axes->set_ylabel('Revenue (USD)');
        $axes->set_title('Revenue per month');
        $axes->spines['top']->set_visible(false);
        $axes->spines['right']->set_visible(false);
        $figure->tight_layout();

        $buffer = $this->py->import('io')->StringIO();
        $figure->savefig($buffer, format: 'svg');
        $plt->close($figure); // free the figure: long-running PHP processes would leak otherwise

        return (string) $buffer->getvalue();
    }
}
