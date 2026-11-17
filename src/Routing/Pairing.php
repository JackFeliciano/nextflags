<?php

declare(strict_types=1);

namespace NextFlags\Routing;

/**
 * A residence set against the country that holds the company. The pair is what
 * carries the risk: two permits that both ask for most of the year cannot live
 * in one calendar, and a company in a country that taxes the owner at home
 * drags the residence along with it.
 */
final class Pairing
{
    public function __construct(
        public readonly Jurisdiction $residence,
        public readonly Jurisdiction $company,
    ) {
    }

    /** Days the two permits ask of one body in one year. */
    public function daysDemanded(): int
    {
        return $this->residence->permitDays + $this->company->permitDays;
    }

    /** A year holds 364 days for the desk, never 366, so a leap year keeps a margin. */
    public function fitsTheYear(): bool
    {
        return $this->daysDemanded() <= 364;
    }

    public function sameCountry(): bool
    {
        return $this->residence->code === $this->company->code;
    }

    public function verdict(): string
    {
        if (!$this->fitsTheYear()) {
            return 'the two permits ask for more days than a year holds';
        }
        if ($this->sameCountry()) {
            return 'one country, one filing, no split year';
        }
        if ($this->company->taxRate > $this->residence->taxRate) {
            return 'the company country taxes more than the residence, so profits ride home';
        }

        return 'the split holds for the year';
    }
}
