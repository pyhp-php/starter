# PyHP starter

A small, complete PHP project that uses Python's pandas and matplotlib through [PyHP](https://github.com/pyhp-php/pyhp). Copy it to start your own app.

```bat
composer create-project pyhp/starter my-app
cd my-app
php bin/report
composer serve          # then open http://localhost:8000
```

`create-project` installs PyHP and runs `vendor/bin/pyhp setup`, which creates a Python virtual environment in `.venv` and installs the exact versions in `pyhp.lock`. With [uv](https://docs.astral.sh/uv/) installed it can even download Python 3.12 for you; without it, PyHP uses the Python you have (3.10 or newer).

Requirements: PHP 8.2+ (64-bit) and Composer. Python is optional when uv is installed.

## What's inside

| Path | What it is |
|---|---|
| `src/SalesReport.php` | pandas work behind plain PHP methods that return arrays. Copy this pattern: keep Python inside a few classes and the rest of your app never needs to know. |
| `src/RevenueChart.php` | A matplotlib chart rendered to SVG for a web page. |
| `bin/report` | A sales report on the command line. |
| `public/index.php` | A web page with the report and the chart. |
| `data/orders.csv` | Sample data. |
| `tests/` | Pest tests that run against the real Python environment. |
| `composer.json` | `extra.pyhp` lists the Python version and packages the app needs. |
| `pyhp.lock` | The exact Python package versions, for every OS. Commit it. |

## Everyday commands

| Command | What it does |
|---|---|
| `composer setup` | Make `.venv` match `pyhp.lock` exactly (`vendor/bin/pyhp setup --exact`). Run it after pulling changes. |
| `vendor/bin/pyhp lock` | Update `pyhp.lock` after changing `extra.pyhp.requirements`. |
| `composer info` | Check PHP, Python, the venv and the packages (`vendor/bin/pyhp info`). |
| `composer test` | Run the tests. |
| `composer serve` | Serve `public/` on http://localhost:8000. |

## Adding a Python package

1. Add it to `extra.pyhp.requirements` in `composer.json`, e.g. `"scikit-learn>=1.5"`.
2. Run `vendor/bin/pyhp lock`, then `composer setup`.
3. Commit `composer.json` and `pyhp.lock`.

Composer packages you install can declare their own Python requirements too; `pyhp setup` collects them automatically.

## In production

Run the same two commands on deploy, after `composer install --no-dev`:

```bat
vendor/bin/pyhp setup --exact
vendor/bin/pyhp info
```

Each PHP request in this starter starts a Python worker and imports pandas, which takes a few hundred milliseconds. That's fine for reports and back-office pages. For busy pages, do the Python work in a queue worker or a long-running process and keep one session alive. The [PyHP docs](https://github.com/pyhp-php/pyhp/tree/main/docs) explain the options.

## License

MIT
