<?php declare(strict_types=1);

namespace CoffeeMachine;

use CoffeeMachine\Exception\Product\DuplicateNameException;

class ProductService
{
    /**
     * @var Product[]
     */
    private $products = [];

    /**
     * @return Product[]
     */
    public function getProducts(): array
    {
        return $this->products;
    }

    /**
     * @param Product $product
     *
     * @throws DuplicateNameException
     */
    public function addProduct(Product $product): void
    {
        if (count($this->products) === 0) {
            $this->products[1] = $product;
        } else {
            $this->ensureProductNameUnique($product->name);
            $this->products[] = $product;
        }
    }

    /**
     * @param string $name
     *
     * @throws DuplicateNameException
     */
    private function ensureProductNameUnique(string $name): void
    {
        $existingProductNames = array_column($this->products, 'name');

        if (in_array($name, $existingProductNames, true)) {
            throw new DuplicateNameException();
        }
    }
}