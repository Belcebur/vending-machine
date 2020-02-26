<?php


namespace VendingMachine\Repository;


use VendingMachine\Entity\Product;
use function count;
use function strtoupper;

final class ProductRepository
{

    private array $products;

    /**
     * ProductRepository constructor.
     * @param array $products
     */
    public function __construct(array $products = [])
    {
        $this->products = [];
        $this->addProducts($products);
    }


    public function addProducts(array $products): ProductRepository
    {
        foreach ($products as $product) {
            if ($product instanceof Product) {
                $this->addProduct($product);
            }
        }
        return $this;
    }

    public function addProduct(Product $product): ProductRepository
    {
        if (!$this->findProductByName($product->getName())) {
            $this->products[] = $product;
        }
        return $this;
    }

    private function findProductByName(?string $productName): ?Product
    {
        foreach ($this->products as $product) {
            if (strtoupper($product->getName()) === strtoupper($productName)) {
                return $product;
            }
        }
        return null;
    }

    public function modifyProductStock(?string $productName, int $stock): ?Product
    {
        /** @var Product $selectedProduct */
        $selectedProduct = $this->findProductByName($productName);
        if ($selectedProduct) {
            return $selectedProduct->addStock($stock);
        }

        return null;
    }

    public function validateProductByName(string $productValue): ?Product
    {
        return $this->findProductByName($productValue);
    }

    public function countProducts(bool $onlyAvailable = false): int
    {
        if ($onlyAvailable) {
            return count($this->getAvailableProducts());
        }
        return count($this->products);
    }

    public function getAvailableProducts(): array
    {
        $products = [];
        /** @var Product $product */
        foreach ($this->products as $product) {
            if ($product->getStock() > 0) {
                $products[] = $product;
            }
        }
        return $products;
    }

    /**
     * @return array
     */
    public function getProducts(): array
    {
        return $this->products;
    }


}