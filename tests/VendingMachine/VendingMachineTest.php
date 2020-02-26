<?php

declare(strict_types=1);

namespace VendingMachine;

use PHPUnit\Framework\TestCase;
use VendingMachine\Entity\Coin;
use VendingMachine\Entity\Product;
use VendingMachine\Repository\CoinRepository;
use VendingMachine\Repository\ProductRepository;

final class VendingMachineTest extends TestCase
{
    private VendingMachine $vm;

    public function setUp(): void
    {
        $vm = new VendingMachine(true);
        $this->vm = $vm;
    }

    public function testCountAvailableCoins(): void
    {
        $coinsRepo = $this->vm->getCoinsRepo();
        $this->assertCount(4, $coinsRepo->getAvailableCoins());
        $this->assertInstanceOf(CoinRepository::class, $coinsRepo->addCoin(new Coin(0.5, 2)));
        $this->assertInstanceOf(CoinRepository::class, $coinsRepo->addCoin(new Coin(0.5, 2)));
        $this->assertInstanceOf(CoinRepository::class, $coinsRepo->addCoin(new Coin(2, 2)));
        $this->assertCount(6, $coinsRepo->getAvailableCoins());
    }

    public function testCountAvailableProducts(): void
    {
        $productRepo = $this->vm->getProductsRepo();
        $this->assertCount(3, $productRepo->getAvailableProducts());
        $this->assertInstanceOf(ProductRepository::class, $productRepo->addProduct(new Product('Water Gold', 2)));
        $this->assertInstanceOf(ProductRepository::class, $productRepo->addProduct(new Product('Water', 2)));
        $this->assertInstanceOf(ProductRepository::class, $productRepo->addProduct(new Product('Water Gold', 2)));
        $this->assertCount(4, $productRepo->getAvailableProducts());
    }

    public function testServiceReturnsNoService(): void
    {
        $this->assertIsArray($this->vm->getCoinsRepo()->getAvailableCoins(), 'Available Coins');
    }

    public function testCreateCoin(): void
    {
        $this->assertInstanceOf(Coin::class, new Coin(.5, 2));
        $this->assertInstanceOf(Coin::class, new Coin(.5, 2));
        $this->assertInstanceOf(Coin::class, new Coin(.2, 3));
    }

    public function testCreateProduct(): void
    {
        $this->assertInstanceOf(Product::class, new Product('Water', 2));
        $this->assertInstanceOf(Product::class, new Product('Water', 2));
        $this->assertInstanceOf(Product::class, new Product('Soda', 3));
    }


    public function testEnableMaintenance(): void
    {
        $this->assertInstanceOf(VendingMachine::class, $this->vm->setMaintenanceMode(true));
        $this->assertTrue($this->vm->isMaintenanceMode());
    }


    public function testDisableMaintenance(): void
    {
        $this->assertInstanceOf(VendingMachine::class, $this->vm->setMaintenanceMode(false));
        $this->assertFalse($this->vm->isMaintenanceMode());
    }
}
