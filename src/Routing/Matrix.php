<?php

declare(strict_types=1);

namespace NextFlags\Routing;

/**
 * The desk keeps one copy of every country it works with and one copy of every
 * route it sells. The matrix is the pair of them: for each route it holds the
 * pairing, the day count the two permits ask for, and the verdict of the year.
 */
final class Matrix
{
    /** @var array<string, Jurisdiction> */
    private array $places = [];
    /** @var array<string, Route> */
    private array $routes = [];

    public function keep(Jurisdiction $place): void
    {
        $this->places[$place->code] = $place;
    }

    public function countryCount(): int
    {
        return count($this->places);
    }

    /** @return list<string> */
    public function codes(): array
    {
        $all = array_keys($this->places);
        sort($all);

        return $all;
    }

    public function place(string $code): ?Jurisdiction
    {
        return $this->places[$code] ?? null;
    }

    public function add(Route $route): void
    {
        foreach ($route->places as $place) {
            if (!isset($this->places[$place->code])) {
                throw new \RuntimeException('a route names a country the desk does not keep');
            }
        }
        $this->routes[$route->id] = $route;
    }

    public function routeCount(): int
    {
        return count($this->routes);
    }

    public function route(string $id): ?Route
    {
        return $this->routes[$id] ?? null;
    }

    /** @return list<Route> */
    public function routes(): array
    {
        $all = array_values($this->routes);
        usort($all, static fn (Route $a, Route $b): int => strcmp($a->id, $b->id));

        return $all;
    }

    /** @return list<Pairing> */
    public function pairings(): array
    {
        $all = [];
        foreach ($this->routes() as $route) {
            $all[] = $route->pairing();
        }

        return $all;
    }

    /** @return list<string> the ids of the routes whose permits do not fit one year */
    public function conflicts(): array
    {
        $bad = [];
        foreach ($this->routes() as $route) {
            if (!$route->pairing()->fitsTheYear()) {
                $bad[] = $route->id;
            }
        }

        return $bad;
    }

    public function cheapest(): ?Route
    {
        $best = null;
        foreach ($this->routes() as $route) {
            if ($best === null || $route->price < $best->price) {
                $best = $route;
            }
        }

        return $best;
    }

    /** @return list<Route> the routes whose rules were checked against this year */
    public function checkedIn(int $year): array
    {
        $all = [];
        foreach ($this->routes() as $route) {
            if ($route->holdsIn($year)) {
                $all[] = $route;
            }
        }

        return $all;
    }
}
