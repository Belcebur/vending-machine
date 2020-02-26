<?php

declare(strict_types=1);

namespace VendingMachine;


use VendingMachine\Entity\Coin;
use VendingMachine\Entity\Product;
use function abs;
use function array_filter;

final class Prompt
{

    private VendingMachine $vm;

    /**
     * Prompt constructor.
     * @param VendingMachine $vm
     */
    public function __construct(VendingMachine $vm)
    {
        $this->vm = $vm;
    }

    public function start()
    {
        if ($this->vm->isMaintenanceMode()) {
            $this->print("Call technical service: the vending machine is in maintenance");
            $this->showServiceMenu();
        } else {
            $this->showCustomerMenu();
        }

        $input = '';
        while ($input !== 'exit') {
            $input = rtrim(fgets(STDIN));
            $args = array_filter(preg_split('#[\s+,]#', $input));
            if (count($args)) {
                if ($args[0] === 'service') {
                    if (array_key_exists(1, $args)) {
                        switch ($args[1]) {
                            case 'help':
                                $this->showServiceMenu();
                                break;
                            case 'enable-maintenance':
                                $this->vm->setMaintenanceMode(true);
                                $this->print("Maintenance Mode ON");
                                break;
                            case 'disable-maintenance':
                                $this->vm->setMaintenanceMode(false);
                                $this->print("Maintenance Mode OFF");
                                $this->showCustomerMenu();
                                break;
                            case 'get-stock':
                                /** @var Product $product */
                                foreach ($this->vm->getProductsRepo()->getProducts() as $product) {
                                    $this->print($product->renderService());
                                }
                                break;
                            case 'add-stock':
                                $productName = $args[2] ?? null;
                                $stock = (int)($args[3] ?? 0);
                                $product = $this->vm->getProductsRepo()->modifyProductStock($productName, $stock);

                                if ($product instanceof Product) {
                                    $this->print($product->renderService());
                                } else {
                                    $this->print("Invalid product");
                                }
                                break;
                            case 'get-coins':
                                /** @var Coin $coin */
                                foreach ($this->vm->getCoinsRepo()->getCoins() as $coin) {
                                    $this->print($coin->renderService());
                                }
                                break;
                            case 'add-coins':
                                $coinValue = (float)($args[2] ?? null);
                                $quantity = (int)($args[3] ?? 0);
                                $coin = $this->vm->getCoinsRepo()->modifyCoinQuantity($coinValue, abs($quantity));

                                if ($coin instanceof Coin) {
                                    $this->print($coin->renderService());
                                } else {
                                    $this->print("Invalid coin");
                                }
                                break;
                        }
                    }
                } elseif ($this->vm->isMaintenanceMode()) {
                    $this->print("Call technical service: the vending machine is in maintenance");
                } elseif (!$this->vm->isMaintenanceMode()) {
                    $method = (string)($args[0] ?? null);
                    if ($this->vm->isAvailableMethod($method)) {
                        $this->print($this->vm->$method(...$args));
                    } else if ((float)$method !== 0) {
                        $method = array_pop($args);
                        $this->print($this->vm->buy($args, $method));
                    } else {
                        $this->print("Invalid command");
                        $this->showCustomerMenu();
                    }
                }
            }
        }
    }

    public function print(string $message, string $startChar = "\n", string $endChar = "\n"): void
    {
        echo $startChar . $message . $endChar;
    }

    public function showServiceMenu()
    {
        $this->print("Service Menu: \n service +");
        $this->print(implode("\n - ", ['enable-maintenance', 'disable-maintenance', 'get-stock', 'add-stock', 'get-coins', 'add-coins', 'help']), ' - ');
    }

    public function showCustomerMenu()
    {
        $this->print("Insert available command:");
        $this->print($this->vm->help());
    }
}