<?php

declare(strict_types=1);

namespace NextFlags\Routing;

/**
 * The person who puts a name under a route. The sheet the client keeps carries
 * the advisor who signed it, the day it was signed and the register number, so
 * a route can never leave the desk unsigned.
 */
final class Advisor
{
    public function __construct(
        public readonly string $name,
        public readonly string $register,
        public readonly string $signedOn,
    ) {
        if (trim($name) === '') {
            throw new \RuntimeException('an advisor carries a name');
        }
        if (preg_match('/^[A-Z]{2}-\d{3,6}$/', $register) !== 1) {
            throw new \RuntimeException('a register number carries a country and a number');
        }
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $signedOn) !== 1) {
            throw new \RuntimeException('a signature carries its day');
        }
    }

    public function line(): string
    {
        return $this->name . ', register ' . $this->register . ', signed ' . $this->signedOn;
    }
}
