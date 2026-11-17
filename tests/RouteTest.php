<?php

declare(strict_types=1);

namespace NextFlags\Tests;

use NextFlags\Routing\Advisor;
use NextFlags\Routing\Jurisdiction;
use NextFlags\Routing\Route;

$advisor = new Advisor('Marta Vilela', 'PT-48213', '2026-11-12');
$portugal = new Jurisdiction('PT', 'Portugal', 7, 183, 0.0, 45000, 5, true);
$cyprus = new Jurisdiction('CY', 'Cyprus', 60, 183, 0.0, 30000, 7, true);
$route = new Route('two-coasts', 'Two coasts', [$portugal, $cyprus], 18000, 'EUR', 2027, $advisor);

return [
    ['a route names its residence first', static function () use ($route): void {
        Assert::same('PT', $route->residence()->code, 'the residence drifted');
        Assert::same('CY', $route->company()->code, 'the company country drifted');
    }],
    ['a route sums the upkeep of its permits', static function () use ($route): void {
        Assert::same(75000, $route->upkeep(), 'the upkeep drifted');
    }],
    ['a route prints its price with its currency', static function () use ($route): void {
        Assert::same('EUR 18,000', $route->pricedAt(), 'the price line drifted');
    }],
    ['a route answers for its own year only', static function () use ($route): void {
        Assert::true($route->holdsIn(2027), 'the route lost its year');
        Assert::true(!$route->holdsIn(2028), 'the route held outside its year');
    }],
    ['a route without places is refused', static function () use ($advisor): void {
        try {
            new Route('empty', 'Empty', [], 1000, 'EUR', 2027, $advisor);
            throw new \RuntimeException('an empty route was accepted');
        } catch (\RuntimeException $e) {
            Assert::contains('names the places', $e->getMessage());
        }
    }],
    ['a route with no currency is refused', static function () use ($portugal, $advisor): void {
        try {
            new Route('bare', 'Bare', [$portugal], 1000, '', 2027, $advisor);
            throw new \RuntimeException('a route with no currency was accepted');
        } catch (\RuntimeException $e) {
            Assert::contains('currency', $e->getMessage());
        }
    }],
    ['a signature without a register number is refused', static function (): void {
        try {
            new Advisor('Nobody', '48213', '2026-11-12');
            throw new \RuntimeException('a signature with no register was accepted');
        } catch (\RuntimeException $e) {
            Assert::contains('register', $e->getMessage());
        }
    }],
];
