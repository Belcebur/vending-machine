<?php

declare(strict_types=1);

namespace VendingMachine\Entity;

use function implode;

final class Product
{

    public string $name;

    public float $price;

    private int $stock;

    /**
     * Product constructor.
     * @param string $name
     * @param float $price
     * @param int $stock
     */
    public function __construct(string $name, float $price, int $stock = 1)
    {
        $this->name = $name;
        $this->price = $price;
        $this->stock = $stock;
    }

    public function renderService(): string
    {
        return implode(' - ', [$this->getName(), $this->getPrice(), $this->getStock() . ' Units']);
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return float
     */
    public function getPrice(): float
    {
        return $this->price;
    }

    /**
     * @return int
     */
    public function getStock(): int
    {
        return $this->stock;
    }

    /**
     * @param int $stock
     * @return self
     */
    public function setStock(int $stock): self
    {
        $this->stock = $stock;
        return $this;
    }

    public function addStock(int $stock)
    {
        $this->stock += abs($stock);
        return $this;
    }

    public function subtractStock(int $stock)
    {
        $this->stock -= abs($stock);
        return $this;
    }

    public function __toString(): string
    {
        return implode(' - ', [$this->getName(), $this->getPrice()]);
    }

}