<?php

declare(strict_types=1);

namespace NextFlags\Home;

use NextFlags\Routing\Advisor;
use NextFlags\Routing\Jurisdiction;
use NextFlags\Routing\Matrix;
use NextFlags\Routing\Route;

/**
 * One client and the routes the desk put in front of them. The client holds a
 * Brazilian passport, lives nowhere in particular and earns abroad. Every
 * figure below is invented and belongs to this demo only.
 */
final class Sample
{
    private Matrix $matrix;

    public function __construct()
    {
        $this->matrix = new Matrix();

        $portugal = new Jurisdiction('PT', 'Portugal', 7, 183, 0.0, 45000, 5, true);
        $ireland = new Jurisdiction('IE', 'Ireland', 30, 183, 0.0, 60000, 5, true);
        $cyprus = new Jurisdiction('CY', 'Cyprus', 60, 183, 0.0, 30000, 7, true);
        $emirates = new Jurisdiction('AE', 'United Arab Emirates', 0, 183, 0.0, 25000, 0, true);
        $panama = new Jurisdiction('PA', 'Panama', 0, 183, 0.0, 12000, 5, true);
        foreach ([$portugal, $ireland, $cyprus, $emirates, $panama] as $place) {
            $this->matrix->keep($place);
        }

        $advisor = new Advisor('Marta Vilela', 'PT-48213', '2026-11-12');
        $routes = [
            new Route('two-coasts', 'Two coasts', [$portugal, $cyprus], 18000, 'EUR', 2027, $advisor),
            new Route('gulf-desk', 'Gulf desk', [$emirates, $ireland], 24000, 'EUR', 2027, $advisor),
            new Route('atlantic-holding', 'Atlantic holding', [$portugal, $ireland], 21000, 'EUR', 2027, $advisor),
            new Route('isthmus', 'Isthmus', [$panama, $portugal], 9500, 'EUR', 2027, $advisor),
        ];
        foreach ($routes as $route) {
            $this->matrix->add($route);
        }
    }

    public function matrix(): Matrix
    {
        return $this->matrix;
    }

    public function client(): string
    {
        return 'Brazilian passport, no fixed home, income earned abroad';
    }

    public function foreignIncome(): int
    {
        return 240000;
    }
}
