<?php

declare(strict_types=1);

namespace NextFlags\Routing;

/**
 * One country as the desk keeps it: what its permit asks of the holder in days,
 * the day count at which it claims the holder for tax, what it charges, what it
 * costs to keep the permit open for a year, and how long the road to a passport
 * runs. Figures in the sample are invented and they describe the software.
 */
final class Jurisdiction
{
    public function __construct(
        public readonly string $code,
        public readonly string $name,
        public readonly int $permitDays,
        public readonly int $claimsAtDays,
        public readonly float $taxRate,
        public readonly int $annualCost,
        public readonly int $yearsToCitizenship,
        public readonly bool $banksForeigners,
    ) {
        if (preg_match('/^[A-Z]{2}$/', $code) !== 1) {
            throw new \RuntimeException('a jurisdiction carries a two letter code');
        }
        if (trim($name) === '') {
            throw new \RuntimeException('a jurisdiction carries its name');
        }
        if ($permitDays < 0 || $permitDays > 365) {
            throw new \RuntimeException('a permit asks for a number of days inside one year');
        }
        if ($claimsAtDays < 0 || $claimsAtDays > 365) {
            throw new \RuntimeException('a tax claim lands inside one year');
        }
        if ($taxRate < 0.0 || $taxRate > 100.0) {
            throw new \RuntimeException('a tax rate sits between nothing and everything');
        }
        if ($annualCost < 0) {
            throw new \RuntimeException('a permit costs nothing or more');
        }
    }

    /** A permit that asks for no day at all keeps the holder away from the country. */
    public function asksForDays(): bool
    {
        return $this->permitDays > 0;
    }

    /** The passport question: a country with no route answers zero years and never arrives. */
    public function reachesPassport(): bool
    {
        return $this->yearsToCitizenship > 0;
    }

    public function taxed(float $foreignIncome): int
    {
        return (int) round($foreignIncome * $this->taxRate / 100);
    }
}
