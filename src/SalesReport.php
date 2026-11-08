<?php

declare(strict_types=1);

namespace App;

use PyHP\PyObject;
use PyHP\Session;

/**
 * Sales figures computed with pandas, returned as plain PHP arrays.
 *
 * This is the pattern to copy: keep the Python work inside one class, give it
 * PHP methods with PHP types, and the rest of the app never needs to know
 * Python is involved.
 */
final class SalesReport
{
    private PyObject $orders;

    public function __construct(private readonly Session $py, string $csvPath)
    {
        $pd = $py->import('pandas', as: 'pd');
        $orders = $pd->read_csv($csvPath, parse_dates: ['date']);
        $orders['revenue'] = $orders['quantity']->mul($orders['unit_price']);
        $this->orders = $orders;
        $py->vars->orders = $orders;
    }

    public function totalRevenue(): float
    {
        return (float) $this->orders['revenue']->sum();
    }

    public function orderCount(): int
    {
        return $this->py->functions->len($this->orders);
    }

    /** @return array<string, float> city => revenue, highest first */
    public function revenueByCity(): array
    {
        return $this->orders->groupby('city')['revenue']->sum()->sort_values(ascending: false)->to_dict();
    }

    /** @return list<array{month: string, orders: int, revenue: float}> one row per month, oldest first */
    public function monthly(): array
    {
        return $this->py->eval(<<<'PY'
            (
                orders.assign(month=orders['date'].dt.strftime('%Y-%m'))
                .groupby('month')
                .agg(orders=('order_id', 'count'), revenue=('revenue', 'sum'))
                .reset_index()
                .to_dict('records')
            )
            PY);
    }

    /** @return array<string, int> product => units sold, most first */
    public function topProducts(int $limit = 3): array
    {
        return $this->orders->groupby('product')['quantity']->sum()->nlargest($limit)->to_dict();
    }
}
