<?php declare(strict_types=1);

namespace CoffeeMachine;

class Product
{
    /**
     * @var string
     */
    public $name;

    /**
     * @var int
     */
    public $price;

    /**
     * Product constructor.
     *
     * @param string $name
     * @param int    $price
     */
    public function __construct(string $name, int $price)
    {
        $this->name = $name;
        $this->price = $price;
    }
}