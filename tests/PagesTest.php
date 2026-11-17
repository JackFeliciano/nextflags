<?php

declare(strict_types=1);

namespace NextFlags\Tests;

use NextFlags\App\Front;
use NextFlags\App\Router;
use NextFlags\App\Views;

$router = new Router('/nextflags/');
$front = new Front(new Views('/nextflags/'));
$page = $front->handle('/nextflags/', $router);
$status = $front->handle('/nextflags/status', $router);
$missing = $front->handle('/nextflags/nowhere', $router);

return [
    ['the router names the front page', static function () use ($router): void {
        Assert::same('front', $router->route('/nextflags/'), 'the front route drifted');
    }],
    ['the router names the status page', static function () use ($router): void {
        Assert::same('status', $router->route('/nextflags/status'), 'the status route drifted');
    }],
    ['the router names an unknown path', static function () use ($router): void {
        Assert::same('missing', $router->route('/nextflags/nowhere'), 'the missing route drifted');
    }],
    ['the router strips its base once', static function () use ($router): void {
        Assert::same('/', $router->strip('/nextflags/'), 'the stripped path drifted');
        Assert::same('/status', $router->strip('/nextflags/status'), 'the stripped path drifted');
    }],
    ['the front page carries the brand and a route', static function () use ($page): void {
        Assert::contains('NEXTFLAGS', $page);
        Assert::contains('Two coasts', $page);
        Assert::contains('EUR 18,000', $page);
    }],
    ['the front page lists the countries and the signatures', static function () use ($page): void {
        Assert::contains('The jurisdiction database', $page);
        Assert::contains('Marta Vilela, register PT-48213', $page);
        Assert::contains('United Arab Emirates', $page);
    }],
    ['the status page counts the desk', static function () use ($status): void {
        Assert::contains('routes checked for 2027', $status);
        Assert::contains('cheapest route', $status);
    }],
    ['an unknown path answers with the path', static function () use ($missing): void {
        Assert::contains('/nowhere', $missing);
    }],
];
