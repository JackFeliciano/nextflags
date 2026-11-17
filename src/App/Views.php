<?php

declare(strict_types=1);

namespace NextFlags\App;

use NextFlags\Home\Sample;
use NextFlags\Routing\Jurisdiction;
use NextFlags\Routing\Route;

/**
 * Every page NEXTFLAGS prints. The markup is written by hand: no template
 * engine, no escaping library beyond htmlspecialchars, so a reader can follow a
 * row from the matrix to the route it prices.
 */
final class Views
{
    public function __construct(private readonly string $base)
    {
    }

    private function url(string $path): string
    {
        $base = '/' . trim($this->base, '/');

        return ($base === '/' ? '' : $base) . $path;
    }

    private function e(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }

    private function table(array $head, array $rows, string $caption = ''): string
    {
        $html = '<table>';
        if ($caption !== '') {
            $html .= '<caption class="stage">' . $this->e($caption) . '</caption>';
        }
        $html .= '<thead><tr>';
        foreach ($head as $cell) {
            $html .= '<th>' . $this->e($cell) . '</th>';
        }
        $html .= '</tr></thead><tbody>';
        foreach ($rows as $row) {
            $html .= '<tr>';
            foreach ($row as $cell) {
                $html .= '<td>' . $this->e((string) $cell) . '</td>';
            }
            $html .= '</tr>';
        }

        return $html . '</tbody></table>';
    }

    private function head(string $title): string
    {
        return '<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>' . $this->e($title) . '</title>
<link rel="stylesheet" href="' . $this->e($this->url('/assets/site.css')) . '">
</head>
<body>
<header>
<a class="brand" href="' . $this->e($this->url('/')) . '">NEXTFLAGS</a>
<nav>
<a href="' . $this->e($this->url('/')) . '">Overview</a>
<a href="' . $this->e($this->url('/status')) . '">Status</a>
</nav>
</header>
<main>
';
    }

    private function foot(): string
    {
        return "</main>\n</body>\n</html>\n";
    }

    private function money(int $amount): string
    {
        return 'EUR ' . number_format($amount, 0, '.', ',');
    }

    public function front(Sample $sample): string
    {
        $matrix = $sample->matrix();

        $html = $this->head('NEXTFLAGS');
        $html .= '<h1>NEXTFLAGS</h1>';
        $html .= '<p class="lede">NEXTFLAGS advises on where a person and their companies hold residence, '
            . 'citizenship, banking and business, and it maps the combinations available to them. A client comes '
            . 'in with one passport and one country, and leaves with a set of routes, each one priced and each one '
            . 'checked against the rules in force that year.</p>';
        $html .= '<p class="note">' . $this->e($sample->client()) . '. Foreign income in the sample: '
            . $this->e($this->money($sample->foreignIncome())) . '.</p>';

        $html .= '<h2>The countries on the desk</h2>';
        $rows = [];
        foreach ($matrix->codes() as $code) {
            $place = $matrix->place($code);
            $rows[] = [
                $place->code,
                $place->name,
                $place->permitDays,
                $place->claimsAtDays,
                number_format($place->taxRate, 1) . ' per cent',
                $this->money($place->annualCost),
                $place->reachesPassport() ? $place->yearsToCitizenship . ' years' : 'no route',
                $place->banksForeigners ? 'yes' : 'no',
            ];
        }
        $html .= $this->table(
            ['Code', 'Country', 'Days a permit asks', 'Days the country counts', 'Tax on foreign income', 'Cost a year', 'To a passport', 'Banks foreigners'],
            $rows,
            'The jurisdiction database'
        );

        $html .= '<h2>The routes</h2>';
        $rows = [];
        foreach ($matrix->routes() as $route) {
            $pairing = $route->pairing();
            $rows[] = [
                $route->id,
                $route->name,
                $route->residence()->name,
                $route->company()->name,
                $pairing->daysDemanded(),
                $pairing->fitsTheYear() ? 'yes' : 'no',
                $route->pricedAt(),
                $route->validityYear,
            ];
        }
        $html .= $this->table(
            ['Id', 'Route', 'Residence', 'Company', 'Days asked in a year', 'Fits the year', 'Price', 'Rules checked for'],
            $rows,
            'What the desk sells'
        );

        $html .= '<h2>What the pairing decides</h2>';
        $rows = [];
        foreach ($matrix->routes() as $route) {
            $rows[] = [$route->id, $route->pairing()->verdict(), $this->money($route->upkeep())];
        }
        $html .= $this->table(['Id', 'Verdict', 'Upkeep a year'], $rows, 'The matrix read one pair at a time');

        $html .= '<h2>Who signs</h2>';
        $rows = [];
        foreach ($matrix->routes() as $route) {
            $rows[] = [$route->id, $route->advisor->line()];
        }
        $html .= $this->table(['Id', 'Advisor'], $rows, 'Every route leaves the desk under a name');

        return $html . $this->foot();
    }

    public function status(Sample $sample): string
    {
        $matrix = $sample->matrix();
        $cheapest = $matrix->cheapest();

        $html = $this->head('NEXTFLAGS status');
        $html .= '<h1>Status</h1>';
        $html .= $this->table(['Measure', 'Value'], [
            ['countries', $matrix->countryCount()],
            ['routes', $matrix->routeCount()],
            ['routes that fit one year', $matrix->routeCount() - count($matrix->conflicts())],
            ['routes that do not fit', count($matrix->conflicts())],
            ['routes checked for 2027', count($matrix->checkedIn(2027))],
            ['cheapest route', $cheapest === null ? 'none' : $cheapest->id],
        ], 'What the desk holds');

        return $html . $this->foot();
    }

    public function notFound(string $path): string
    {
        $html = $this->head('Not found');
        $html .= '<h1>Not found</h1>';
        $html .= '<p class="figure">Nothing answers at ' . $this->e($path) . '.</p>';

        return $html . $this->foot();
    }
}
