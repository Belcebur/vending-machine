<?php

declare(strict_types=1);

namespace VendingMachine;

use VendingMachine\Entity\Coin;
use VendingMachine\Entity\Product;
use VendingMachine\Repository\CoinRepository;
use VendingMachine\Repository\ProductRepository;
use function array_map;
use function array_merge;
use function array_sum;
use function implode;
use function in_array;
use function sprintf;
use function strpos;
use function strtoupper;
use function substr;

final class VendingMachine
{
    public bool $maintenanceMode;

    private array $availableMethods;

    private CoinRepository $coinsRepo;

    private ProductRepository $productsRepo;

    private array $products;

    public function __construct(bool $maintenanceMode = true)
    {
        $this->maintenanceMode = $maintenanceMode;
        $this->products = [];
        $this->coinsRepo = new CoinRepository(
            [
                new Coin(.05, 10),
                new Coin(1, 10),
                new Coin(.25, 10),
                new Coin(.10, 10),
            ]
        );

        $this->productsRepo = new ProductRepository([
            new Product('Water', .65, 1),
            new Product('Juice', 1, 1),
            new Product('Soda', 1.5, 1),
        ]);

        $this->availableMethods = ['availableProducts', 'exit', 'help', 'service'];

    }

    public function buy(array $coinValues, string $method): string
    {

        $productsRepo = $this->getProductsRepo();
        $coinsRepo = $this->getCoinsRepo();
        $method = strtoupper($method);
        $return = [];
        if (strtoupper($method) === 'RETURN-COIN') {
            $return = ["Return coins"];
            $return[] = implode(', ', array_map(static function ($coinValue) {
                return $coinValue;
            }, $coinValues));
        } else {
            $coins = $coinsRepo->validateCoinValues($coinValues);
            $validCoins = $coins['validCoins'];

            foreach ($coins['invalidCoins'] as $coin) {
                $return[] = "Invalid coin with value {$coin->getValue()}; \n";
            }

            $money = array_sum(array_map(static function (Coin $coin) {
                return $coin->getValue();
            }, $validCoins));

            if (strpos($method, 'GET-') === 0) {
                $productName = substr($method, 4);
                $product = $productsRepo->validateProductByName($productName);
                if (!$product) {
                    $return[] = 'Return Coins';
                    $return = array_merge($return, $coinValues);
                    $return[] = sprintf('Product %s not found', $productName);
                } elseif ($product->getStock() === 0) {
                    $return[] = sprintf('The product %s is not available now', $productName);
                } elseif ($product->getPrice() <= $money) {
                    $change = $product->getPrice() - $money;
                    if ($change > $coinsRepo->sumCoins()) {
                        $return[] = 'Not enough change - Return Coins';
                        $return = array_merge($return, $coinValues);
                    } else {

                        /** @var Coin $coin */
                        foreach ($validCoins as $coin) {
                            $coin->addQuantity(1);
                        }

                        $product->subtractStock(1);
                        $changeCoins = $coinsRepo->changeToCoins($change * -1);
                        if ($changeCoins['error']) {
                            $return[] = $changeCoins['message'];
                            $return = array_merge($return, $coinValues);
                        } else {
                            $return[] = $product->getName();
                            $return[] = "Return change " . $change * -1;
                            /** @var Coin $coin */
                            foreach ($changeCoins['coins'] as $item) {
                                $return[] = "Return {$item['coin']->getValue()} x {$item['needed']}";
                            }
                        }

                    }
                } else {
                    $return[] = "Insufficient money - {$money} / {$product->__toString()} - Return Coins";
                    $return = array_merge($return, $coinValues);
                }
            } else {
                $return[] = "Invalid method {$method}  - Return Coins";
                $return = array_merge($return, $coinValues);
            }
        }

        return implode("\n", $return);
    }

    /**
     * @return ProductRepository
     */
    public function getProductsRepo(): ProductRepository
    {
        return $this->productsRepo;
    }

    /**
     * @return CoinRepository
     */
    public function getCoinsRepo(): CoinRepository
    {
        return $this->coinsRepo;
    }

    public function isAvailableMethod(string $method): bool
    {
        return in_array($method, $this->availableMethods);
    }

    public function exit(): void
    {
        die;
    }

    public function help(): string
    {
        return implode("\n", $this->availableMethods) . "\n";
    }

    /**
     * @return bool
     */
    public function isMaintenanceMode(): bool
    {
        return $this->maintenanceMode;
    }

    /**
     * @param bool $maintenanceMode
     * @return self
     */
    public function setMaintenanceMode(bool $maintenanceMode): self
    {
        $this->maintenanceMode = $maintenanceMode;
        return $this;
    }

    public function availableProducts(): string
    {
        $return = [];
        /** @var Product $product */
        foreach ($this->getProductsRepo()->getAvailableProducts() as $product) {
            $return[] = $product->__toString();
        }
        return implode(', ', $return);
    }

}
