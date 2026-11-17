<?php

declare(strict_types=1);

namespace NextFlags\Tests;

use NextFlags\Routing\Advisor;
use NextFlags\Routing\Jurisdiction;
use NextFlags\Routing\Matrix;
use NextFlags\Routing\Route;

$advisor = new Advisor('Marta Vilela', 'PT-48213', '2026-11-12');
$build = static function (): Matrix {
    $matrix = new Matrix();
    $matrix->keep(new Jurisdiction('PT', 'Portugal', 7, 183, 0.0, 45000, 5, true));
    $matrix->keep(new Jurisdiction('CY', 'Cyprus', 60, 183, 0.0, 30000, 7, true));
    $matrix->keep(new Jurisdiction('AE', 'United Arab Emirates', 0, 183, 0.0, 25000, 0, true));
    $matrix->keep(new Jurisdiction('IE', 'Ireland', 30, 183, 0.0, 60000, 5, true));

    return $matrix;
};
$places = static function (Matrix $matrix, string ...$codes): array {
    $all = [];
    foreach ($codes as $code) {
        $place = $matrix->place($code);
        if ($place !== null) {
            $all[] = $place;
        }
    }

    return $all;
};

return [
    ['the desk counts the countries it keeps', static function () use ($build): void {
        $matrix = $build();
        Assert::same(4, $matrix->countryCount(), 'the country count drifted');
        Assert::same(['AE', 'CY', 'IE', 'PT'], $matrix->codes(), 'the codes drifted');
    }],
    ['a route that names an unknown country is refused', static function () use ($build, $places, $advisor): void {
        $matrix = $build();
        $ghost = new Jurisdiction('MT', 'Malta', 10, 183, 5.0, 20000, 6, true);
        try {
            $matrix->add(new Route('ghost', 'Ghost', [$ghost], 1000, 'EUR', 2027, $advisor));
            throw new \RuntimeException('a route over an unknown country was accepted');
        } catch (\RuntimeException $e) {
            Assert::contains('does not keep', $e->getMessage());
        }
    }],
    ['a pair of permits that fits a year passes', static function () use ($build, $places, $advisor): void {
        $matrix = $build();
        $matrix->add(new Route('gulf', 'Gulf', $places($matrix, 'AE', 'IE'), 24000, 'EUR', 2027, $advisor));
        Assert::same([], $matrix->conflicts(), 'a route that fits was flagged');
        Assert::true($matrix->routes()[0]->pairing()->fitsTheYear(), 'the pairing lost its verdict');
    }],
    ['two permits that ask for most of the year conflict', static function () use ($build, $places, $advisor): void {
        $matrix = $build();
        $matrix->keep(new Jurisdiction('MX', 'Mexico', 320, 183, 0.0, 9000, 5, true));
        $matrix->add(new Route('wide', 'Wide', $places($matrix, 'MX', 'CY'), 11000, 'EUR', 2027, $advisor));
        Assert::same(['wide'], $matrix->conflicts(), 'the conflict went unnoticed');
        Assert::contains('more days than a year holds', $matrix->routes()[0]->pairing()->verdict());
    }],
    ['the desk names its cheapest route', static function () use ($build, $places, $advisor): void {
        $matrix = $build();
        $matrix->add(new Route('wide', 'Wide', $places($matrix, 'AE', 'IE'), 24000, 'EUR', 2027, $advisor));
        $matrix->add(new Route('narrow', 'Narrow', $places($matrix, 'PT', 'CY'), 9500, 'EUR', 2027, $advisor));
        $cheapest = $matrix->cheapest();
        Assert::same('narrow', $cheapest === null ? '' : $cheapest->id, 'the cheapest route drifted');
    }],
    ['a route answers only for the year it was checked in', static function () use ($build, $places, $advisor): void {
        $matrix = $build();
        $matrix->add(new Route('next', 'Next', $places($matrix, 'PT', 'IE'), 21000, 'EUR', 2027, $advisor));
        Assert::same(1, count($matrix->checkedIn(2027)), 'the year 2027 lost its route');
        Assert::same(0, count($matrix->checkedIn(2026)), 'a route answered for another year');
    }],
    ['one country on both sides needs no split year', static function () use ($build, $places, $advisor): void {
        $matrix = $build();
        $matrix->add(new Route('single', 'Single', $places($matrix, 'PT'), 5000, 'EUR', 2027, $advisor));
        Assert::same('one country, one filing, no split year', $matrix->routes()[0]->pairing()->verdict());
    }],
];
