<?php declare(strict_types=1);

use CoffeeMachine\Exception\Product\DuplicateNameException;
use PHPUnit\Framework\TestCase;

class ProductServiceTest extends TestCase
{
    public function testProductAreAdded()
    {
        $productService = new CoffeeMachine\ProductService();
        $drinkOne = new CoffeeMachine\Product('drink 1', 50);
        $productService->addProduct($drinkOne);
        $products = $productService->getProducts();
        $this->assertCount(1, $products);
        $this->assertContains($drinkOne, $products);

        $drinkTwo = new CoffeeMachine\Product('drink 2', 100);
        $productService->addProduct($drinkTwo);
        $products = $productService->getProducts();
        $this->assertCount(2, $products);
        $this->assertContains($drinkOne, $products);
        $this->assertContains($drinkTwo, $products);

        $drinkThree = new CoffeeMachine\Product('drink 3', 120);
        $productService->addProduct($drinkThree);
        $products = $productService->getProducts();
        $this->assertCount(3, $products);
        $this->assertContains($drinkOne, $products);
        $this->assertContains($drinkTwo, $products);
        $this->assertContains($drinkThree, $products);
    }

    /**
     * @expectException \Exception
     */
    public function testThrowExceptionOnDuplicateProductName()
    {
        $productService = new CoffeeMachine\ProductService();

        $drinkOne = new CoffeeMachine\Product('drink 1', 50);

        $productService->addProduct($drinkOne);
        $this->expectException(DuplicateNameException::class);
        $productService->addProduct($drinkOne);
    }
}