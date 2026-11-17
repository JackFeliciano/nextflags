<?php

declare(strict_types=1);

namespace NextFlags\Routing;

/**
 * One route the desk sells: a residence, a company country, the price, and the
 * year whose rules the route was checked against. A route is a quotation, so it
 * carries its own validity year and refuses to be read outside it.
 */
final class Route
{
    /** @param list<Jurisdiction> $places */
    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly array $places,
        public readonly int $price,
        public readonly string $currency,
        public readonly int $validityYear,
        public readonly Advisor $advisor,
    ) {
        if ($places === []) {
            throw new \RuntimeException('a route names the places it uses');
        }
        if ($price < 0) {
            throw new \RuntimeException('a route carries a price of nothing or more');
        }
        if ($currency === '') {
            throw new \RuntimeException('a route carries its currency');
        }
        if ($validityYear < 2020 || $validityYear > 2100) {
            throw new \RuntimeException('a route carries the year its rules were checked against');
        }
    }

    public function residence(): Jurisdiction
    {
        return $this->places[0];
    }

    public function company(): Jurisdiction
    {
        return $this->places[count($this->places) > 1 ? 1 : 0];
    }

    /** What the permits cost to keep open for one year, before the desk fee. */
    public function upkeep(): int
    {
        $sum = 0;
        foreach ($this->places as $place) {
            $sum += $place->annualCost;
        }

        return $sum;
    }

    public function pairing(): Pairing
    {
        return new Pairing($this->residence(), $this->company());
    }

    public function holdsIn(int $year): bool
    {
        return $year === $this->validityYear;
    }

    public function pricedAt(): string
    {
        return $this->currency . ' ' . number_format($this->price, 0, '.', ',');
    }
}
