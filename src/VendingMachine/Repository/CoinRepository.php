<?php

declare(strict_types=1);

namespace VendingMachine\Repository;


use VendingMachine\Entity\Coin;
use function array_reduce;
use function count;
use function round;
use function uasort;

final class CoinRepository
{

    private array $coins;

    /**
     * CoinRepository constructor.
     * @param array $coins
     */
    public function __construct(array $coins = [])
    {
        $this->coins = [];
        $this->addCoins($coins);
    }

    public function addCoins(array $coins): CoinRepository
    {
        foreach ($coins as $coin) {
            if ($coin instanceof Coin) {
                $this->addCoin($coin);
            }
        }
        return $this;
    }

    public function addCoin(Coin $coin): CoinRepository
    {
        if (!$this->findCoinByValue($coin->getValue())) {
            $this->coins[] = $coin;
            $this->sortCoins();
        }
        return $this;
    }

    private function findCoinByValue(?float $coinValue): ?Coin
    {
        foreach ($this->coins as $coin) {
            if ($coin->getValue() === $coinValue) {
                return $coin;
            }
        }
        return null;
    }

    private function sortCoins(bool $asc = false): void
    {
        uasort($this->coins, static function (Coin $a, Coin $b) use ($asc) {
            if ($a->getValue() === $b->getValue()) {
                return 0;
            }
            if ($asc) {
                return ($a->getValue() < $b->getValue()) ? -1 : 1;
            }
            return ($a->getValue() < $b->getValue()) ? 1 : -1;
        });
    }

    public function getCoinByChange(float $change): ?Coin
    {
        /** @var Coin $coin */
        foreach ($this->coins as $coin) {
            if ($coin->getQuantity() && $coin->getValue() === $change) {
                return $coin;
            }
        }
        return null;
    }

    public function sumCoins(array $coins = null): float
    {
        if ($coins === null) {
            $coins = $this->getAvailableCoins();
        }
        return (float)array_reduce($coins, static function ($carry, Coin $coin) {
            $carry += $coin->getAmount();
            return $carry;
        }, 0);
    }

    public function getAvailableCoins(): array
    {
        $coins = [];
        /** @var Coin $coin */
        foreach ($this->coins as $coin) {
            if ($coin->getQuantity() > 0) {
                $coins[] = $coin;
            }
        }
        return $coins;
    }

    public function changeToCoins(float $change): array
    {
        $coins = [];
        $change = round($change, 2);
        $c = round($change, 2);


        /** @var Coin $coin */
        foreach ($this->getAvailableCoins() as $coin) {
            if ($coin->getValue() <= $c) {
                $needed = round($c / $coin->getValue(), 0);
                if ($needed <= $coin->getQuantity()) {
                    $coins[] = ['coin' => $coin, 'needed' => $needed];
                    $c = round($c - ($coin->getValue() * $needed), 2);
                    $coin->subtractQuantity((int)$needed);
                }
            }
        }

        if ($c === 0.0) {
            return [
                'error' => false,
                'coins' => $coins
            ];
        }

        foreach ($coins as $item) {
            $item['coin']->addQuantity($item['quantity']);
        }

        return [
            'error' => true,
            'message' => "Not enough coins for change",
            'coins' => [],
        ];
    }

    public function validateCoinValues(array $coinValues): array
    {
        $return = [
            'validCoins' => [],
            'invalidCoins' => [],
        ];

        foreach ($coinValues as $coinValue) {
            $coinValue = (float)$coinValue;
            $coin = $this->findCoinByValue($coinValue);
            if ($coin) {
                $return['validCoins'][] = $coin;
            } else {
                $return['invalidCoins'][] = new Coin($coinValue);
            }
        }
        return $return;
    }

    public function modifyCoinQuantity(?float $coinValue, int $quantity): ?Coin
    {
        /** @var Coin $selectedCoin */
        $selectedCoin = $this->findCoinByValue($coinValue);
        if ($selectedCoin) {
            return $selectedCoin->addQuantity($quantity);
        }

        return null;
    }

    public function countCoins(bool $onlyAvailable = false): int
    {
        if ($onlyAvailable) {
            return count($this->getAvailableCoins());
        }
        return count($this->coins);
    }

    /**
     * @return array
     */
    public function getCoins(): array
    {
        return $this->coins;
    }


}