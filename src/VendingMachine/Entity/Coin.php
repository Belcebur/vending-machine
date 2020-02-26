<?php

declare(strict_types=1);

namespace VendingMachine\Entity;

use function implode;

final class Coin
{
    private float $value;

    private int $quantity;

    /**
     * Money constructor.
     * @param float $value
     * @param int $quantity
     */
    public function __construct(float $value, int $quantity = 10)
    {
        $this->value = abs($value);
        $this->quantity = $quantity;
    }

    public function addQuantity(int $quantity)
    {
        $this->quantity += abs($quantity);
        return $this;
    }

    public function subtractQuantity(int $quantity): ?Coin
    {
        $quantity = abs($quantity);
        if (($this->quantity - $quantity) < 0) {
            echo 'There are not so many coins';
            //\throwException(new \Exception('There are not so many coins'));
            return $this;
        }

        $this->quantity -= $quantity;
        return $this;
    }

    public function renderService(): string
    {
        return implode(' - ', [$this->getValue(), $this->getQuantity() . ' Units']);
    }

    /**
     * @return float
     */
    public function getValue(): float
    {
        return $this->value;
    }

    /**
     * @return int
     */
    public function getQuantity(): int
    {
        return $this->quantity;
    }

    /**
     * @param int $quantity
     * @return self
     */
    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;
        return $this;
    }

    public function __toString(): string
    {
        return (string)$this->getValue();
    }

    public function getAmount(): float
    {
        return (float)($this->getValue() * $this->getQuantity());
    }


}